<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
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
     * @param bool $added
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function createNewProductBatchDetails(CProductBatch $productBatch, $products, $added = false){

        /** @var CWorkCategoryStepsRepo $catStepsRepo */
        $catStepsRepo = \Monkey::app()->repoFactory->create('WorkCategorySteps');

        /** @var CWorkCategorySteps $categoryStep */
        $categoryStep = $catStepsRepo->getFirstStepsFromCategoryId($productBatch->contractDetails->workCategory->id);
        $categoryStepId = $categoryStep->id;


        if($added){
            /** @var CObjectCollection $prBDet */
            $prBDet = $productBatch->productBatchDetails;
            $count = 0;
            $extNum = $prBDet->count();
            $isAdded = false;
        }

        foreach ($products as $productId){
            $pId = explode('-',$productId)[0];
            $pVId = explode('-',$productId)[1];

            if($added) {
                /** @var CProductBatchDetails $ext */
                $ext = $prBDet->findOneByKeys(['productId' => $pId, 'productVariantId' => $pVId]);

                if(!$ext){
                    $pBD = $this->getEmptyEntity();
                    $pBD->productid = $pId;
                    $pBD->productVariantId = $pVId;
                    $pBD->productBatchId = $productBatch->id;
                    $pBD->workCategoryStepsId = $categoryStepId;
                    $pBD->smartInsert();

                    $count++;
                    $isAdded = true;
                }
            }

            if(!$added) {
                $pBD = $this->getEmptyEntity();
                $pBD->productid = $pId;
                $pBD->productVariantId = $pVId;
                $pBD->productBatchId = $productBatch->id;
                $pBD->workCategoryStepsId = $categoryStepId;
                $pBD->smartInsert();
            }
        }

        if($isAdded){
            $price = $productBatch->contractDetails->workPriceList->price;
            $newNum = $extNum + $count;
            $newValue = $newNum * $price;

            $productBatch->value = $newValue;
            $productBatch->update();
        }

        return true;
    }

    /**
     * @param $id
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function goToNextStep($id){

        /** @var CProductBatchDetails $pbd */
        $pbd = \Monkey::app()->repoFactory->create('ProductBatchDetails')->findOneBy(['id'=>$id]);

        if(!is_null($pbd->workCategorySteps->rgt)) {
            $pbd->workCategoryStepsId = ($pbd->workCategorySteps->rgt);
            $pbd->update();
        }

        return true;

    }

    /**
     * @param int $batchId
     * @param array $products
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function insertProductInEmptyProductBatch(int $batchId, array $products) {
        /** @var CProductBatch $pb */
        $pb = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$batchId]);

        if(!is_null($pb)){

            /** @var CObjectCollection $pbd */
            $pbd = $pb->productBatchDetails;

            $notContr = is_null($pb->contractDetailsId);


            if($pbd->count() == 0 || ($pbd->count() > 0 && $notContr)) {

                foreach ($products as $productId) {
                    $pId = explode('-', $productId)[0];
                    $pVId = explode('-', $productId)[1];

                    if($pbd->count() > 0){
                        /** @var CProductBatchDetails $ext */
                        $ext = $pbd->findOneByKeys(['productId'=>$pId,'productVariantId'=>$pVId]);
                    }

                    if(isset($ext) && !$ext) {
                        $pBD = $this->getEmptyEntity();
                        $pBD->productid = $pId;
                        $pBD->productVariantId = $pVId;
                        $pBD->productBatchId = $pb->id;
                        $pBD->smartInsert();
                    }
                }
            } else {
                $this->createNewProductBatchDetails($pb, $products, true);
            }

        } else return false;

        return true;
    }

    public function deleteProductFromBatch(int $productBatchId, array $products, $emptyBatch = false){

        foreach ($products as $productId) {
            $pId = explode('-', $productId)[0];
            $pVId = explode('-', $productId)[1];

            /** @var CProductBatchDetails $pbd */
            $pbd = $this->findOneBy(['productId'=>$pId, 'productVariantId'=>$pVId, 'productBatchId'=>$productBatchId]);
            $pbd->delete();
        }

        if(!$emptyBatch){
            /** @var CProductBatch $pb */
            $pb = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$productBatchId]);

            /** @var CObjectCollection $pbds */
            $pbds = $pb->productBatchDetails;
            $newp = $pbds->count();

            $newV = $newp * $pb->contractDetails->workPriceList->price;
            $pb->value = $newV;
            $pb->update();
        }

        return true;

    }


}