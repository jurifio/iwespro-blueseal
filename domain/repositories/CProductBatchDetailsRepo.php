<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CWorkCategorySteps;

/**
 * Class CProductBatchDetailsRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/03/2018
 * @since 1.0
 */
class CProductBatchDetailsRepo extends ARepo
{
    /**
     * @param CProductBatch $productBatch
     * @param $products
     * @return bool
     * @throws BambooException
     */
    public function createNewProductBatchDetails(CProductBatch $productBatch, $products){

        /** @var CWorkCategoryStepsRepo $catStepsRepo */
        $catStepsRepo = \Monkey::app()->repoFactory->create('WorkCategorySteps');

        /** @var CWorkCategorySteps $categoryStep */
        $categoryStep = $catStepsRepo->getFirstStepsFromCategoryId($productBatch->contractDetails->workCategory->id);
        $categoryStepId = $categoryStep->id;

        foreach ($products as $productId){
            $pId = explode('-',$productId)[0];
            $pVId = explode('-',$productId)[1];

            $pBD = $this->getEmptyEntity();
            $pBD->productid = $pId;
            $pBD->productVariantId = $pVId;
            $pBD->productBatchId = $productBatch->id;
            $pBD->workCategoryStepsId = $categoryStepId;
            $pBD->smartInsert();
        }

        return true;
    }
}