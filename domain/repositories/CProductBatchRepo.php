<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CProductBatch;

/**
 * Class CProductBatchRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/03/2018
 * @since 1.0
 */
class CProductBatchRepo extends ARepo
{
    /**
     * @param $scheduledDelivery
     * @param $closingDate
     * @param $value
     * @param $contractDetailsId
     * @param $products
     * @return bool
     */
    public function createNewProductBatch($scheduledDelivery, $closingDate, $value, $contractDetailsId, $products){

        try {
            /** @var CContractDetails $contractDetails */
            $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBY(['id'=>$contractDetailsId]);

            $sectionalCode = $contractDetails->workCategory->sectionalCode;

            /** @var CSectionalRepo $sectionalRepo */
            $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');

            /** @var CProductBatch $productBatch */
            $productBatch = $this->getEmptyEntity();
            $productBatch->scheduledDelivery = $scheduledDelivery;
            $productBatch->closingDate = $closingDate;
            $productBatch->value = $value;
            $productBatch->contractDetailsId = $contractDetailsId;
            $productBatch->sectional = $sectionalRepo->createNewSectionalCode($sectionalCode);
            $productBatch->smartInsert();

        /** @var CProductBatchDetailsRepo $productBatchDetailsRepo */
        $productBatchDetailsRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');
        $productBatchDetailsRepo->createNewProductBatchDetails($productBatch, $products);
        } catch (\Throwable $e){}

        return true;


    }
}