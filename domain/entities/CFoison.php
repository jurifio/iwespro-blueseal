<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

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
 */
class CFoison extends AEntity
{
    protected $entityTable = 'Foison';
    protected $primaryKeys = ['id'];

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
            $ids[] = $interest->workCategoryId;
        }
        return $ids;
    }

    public function nonInterestId(){
        /** @var CObjectCollection $wId */
        $wId = \Monkey::app()->repoFactory->create('WorkCategory')->findAll();

        $allCategory = [];
        /** @var CWorkCategory $workCategory */
        foreach ($wId as $workCategory)
        {
            $allCategory[] = $workCategory->id;
        }

        $iIds = $this->getInterestId();
        return array_diff($allCategory, $iIds);
    }

}