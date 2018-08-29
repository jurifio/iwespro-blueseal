<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchHasProductName;

/**
 * Class CProductBatchHasProductNameRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/08/2018
 * @since 1.0
 */
class CProductBatchHasProductNameRepo extends ARepo
{
    /**
     * USE THIS METHOD ONLY FOR REASSING BATCH
     * @param CProductBatch $productBatch
     * @param $prNames
     * @param $langId
     */
    public function insertNewProductNameFromCopy(CProductBatch $productBatch, $prNames, $langId)
    {

        /** @var CWorkCategoryStepsRepo $workCategoryRepo */
        $workCategoryRepo = \Monkey::app()->repoFactory->create('WorkCategorySteps');
        $initStep = $workCategoryRepo->getFirstStepsFromCategoryId($productBatch->workCategoryId)->id;

        foreach ($prNames as $prName) {

            /** @var CProductBatchHasProductName $pbhpn */
            $pbhpn = $this->getEmptyEntity();
            $pbhpn->productBatchId = $productBatch->id;
            $pbhpn->productName = $prName;
            $pbhpn->langId = $langId;
            $pbhpn->workCategoryStepsId = $initStep;
            $pbhpn->smartInsert();
        }

    }


}