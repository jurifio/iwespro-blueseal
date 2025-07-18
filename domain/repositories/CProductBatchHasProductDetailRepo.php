<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CLang;
use bamboo\domain\entities\CProductBatchHasProductDetail;
use bamboo\domain\entities\CWorkCategory;

/**
 * Class CProductBatchHasProductDetailRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 07/01/2019
 * @since 1.0
 */
class CProductBatchHasProductDetailRepo extends ARepo
{

    public function insertNewDetails(int $productBatchId, array $productDetailIds, int $langId){

        /** @var CWorkCategoryStepsRepo $wCSRepo */
        $wCSRepo = \Monkey::app()->repoFactory->create('WorkCategorySteps');

        $category = 0;
        switch ($langId){
            case 2:
                $category = CWorkCategory::DET_ENG;
                break;
            case 3:
                $category = CWorkCategory::DET_DTC;
                break;
            case 5:
                $category = CWorkCategory::DET_RUS;
                break;
            case 6:
                $category = CWorkCategory::DET_CHI;
                break;
            case 7:
                $category = CWorkCategory::DET_FRE;
                break;
        }

        foreach ($productDetailIds as $productDetailId){

            //Find existent detail in batchs
            $existOldBatchs = $this->findBy([
                'productDetailId'   =>  $productDetailId,
                'langId'            =>  $langId
            ]);

            /** @var CProductBatchHasProductDetail $existOldBatch */
            foreach ($existOldBatchs as $existOldBatch){
                if ($existOldBatch->productBatch->isUnassigned == 0) continue;
            }

            $productBatchHasProductDetails = $this->getEmptyEntity();
            $productBatchHasProductDetails->productBatchId = $productBatchId;
            $productBatchHasProductDetails->productDetailId = $productDetailId;
            $productBatchHasProductDetails->langId = $langId;
            $productBatchHasProductDetails->workCategoryStepsId = $wCSRepo->getFirstStepsFromCategoryId($category)->id;
            $productBatchHasProductDetails->smartInsert();
        }

        return true;

    }

}