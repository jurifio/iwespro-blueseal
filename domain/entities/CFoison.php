<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\utils\time\SDateToolbox;

/**
 * Class CFoison
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/03/2018
 * @since 1.0
 *
 * @property CUser $user
 * @property CObjectCollection $contracts
 * @property CAddressBook $addressBook
 * @property CObjectCollection $foisonHasInterest*
 * @property CObjectCollection $workCategory
 *
 *
 */
class CFoison extends AEntity
{
    protected $entityTable = 'Foison';
    protected $primaryKeys = ['id'];

    CONST MININUM_RANK = 7;

    /**
     * @return array
     */
    public function getInterestId()
    {
        /** @var CObjectCollection $interests */
        $interests = $this->foisonHasInterest;
        $ids = [];
        /** @var CFoisonHasInterest $interest */
        foreach ($interests as $interest) {
            if ($interest->foisonStatusId == 2 OR $interest->foisonStatusId == 3) {
                $ids[] = $interest->workCategoryId;
            }
        }
        return $ids;
    }

    public function nonInterestId()
    {
        /** @var CObjectCollection $wId */
        $wId = \Monkey::app()->repoFactory->create('WorkCategory')->findAll();

        $allCategory = [];
        /** @var CWorkCategory $workCategory */
        foreach ($wId as $workCategory) {
            $allCategory[] = $workCategory->id;
        }

        $iIds = $this->getInterestId();
        return array_diff($allCategory, $iIds);
    }

    public function getContract()
    {
        return $this->contracts->findByKey('isActive', 1);
    }


    /**
     * @return bool
     */
    public function hasOpenedProductBatch()
    {

        if (is_null($this->activeProductBatch)) return false;
        return true;
    }

    /**
     * @param null $months
     * @return CObjectCollection
     */
    public function getClosedTimeRanchProductBatch($months = null)
    {
        if (!is_null($months)) {
            $initDate = SDateToolbox::removeOrAddMonthsFromDate(null, $months, '-');
        } else $initDate = 0;

        $contracts = $this->contracts;
        $pbArray = new CObjectCollection();


        /** @var CContracts $contract */
        foreach ($contracts as $contract) {

            /** @var CObjectCollection $contractDetails */
            $contractDetails = $contract->contractDetails;

            /** @var CContractDetails $contractDetail */
            foreach ($contractDetails as $contractDetail) {

                /** @var CObjectCollection $pbs */
                $pbs = $contractDetail->productBatch;

                /** @var CProductBatch $pb */
                foreach ($pbs as $pb) {

                    if (!is_null($pb->closingDate) && $pb->closingDate >= $initDate && !is_null($pb->timingRank) && (!is_null($pb->qualityRank) || !is_null($pb->operatorRankIwes))) {
                        $pbArray->add($pb);
                    }
                }

            }
        }

        return $pbArray;
    }

    /**
     * @param bool $update
     * @param array $productsBatch
     * @return float
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function totalRank($update = false, $productsBatch = [])
    {

        if(empty($productsBatch)) {
            $pbs = $this->getClosedTimeRanchProductBatch(3);
        } else {
            $pbs = $productsBatch;
        }
        $avgs = [];

        /** @var CProductBatch $pb */
        foreach ($pbs as $pb) {
            $qualityRank = is_null($pb->operatorRankIwes) ? $pb->qualityRank : $pb->operatorRankIwes;

            if ($pb->timingRank == 10 and $qualityRank <= 6) {
                $timingPonderateRank = $qualityRank;
            } else {
                $timingPonderateRank = $pb->timingRank;
            }

            $avgs[] = ($qualityRank + $timingPonderateRank) / 2;
        }

        $sumAvg = 0;
        foreach ($avgs as $avg) {
            $sumAvg += $avg;

        }
        $allAvg = count($avgs) === 0 ? 0 : round($sumAvg / count($avgs), 2);
        if ($update) {
            $this->rank = $allAvg;
            $this->update();
        }
        return $allAvg;
    }


}