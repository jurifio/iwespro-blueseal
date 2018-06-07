<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\entities\CWorkCategorySteps;

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
     * @param $value
     * @param $contractDetailsId
     * @param $products
     * @return \bamboo\core\db\pandaorm\entities\AEntity|CProductBatch
     */
    public function createNewProductBatch($scheduledDelivery, $value, $contractDetailsId, $products){

        try {
            /** @var CContractDetails $contractDetails */
            $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBY(['id'=>$contractDetailsId]);

            $sectionalCodeId = $contractDetails->workCategory->sectionalCodeId;

            /** @var CSectionalRepo $sectionalRepo */
            $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');

            /** @var CProductBatch $productBatch */
            $productBatch = $this->getEmptyEntity();
            $productBatch->scheduledDelivery = $scheduledDelivery;
            $productBatch->value = $value;
            $productBatch->contractDetailsId = $contractDetailsId;
            $productBatch->sectional = $sectionalRepo->createNewSectionalCode($sectionalCodeId);
            $productBatch->smartInsert();

        /** @var CProductBatchDetailsRepo $productBatchDetailsRepo */
        $productBatchDetailsRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');
        $productBatchDetailsRepo->createNewProductBatchDetails($productBatch, $products);
        } catch (\Throwable $e){}

        return $productBatch;
    }


    /**
     * @param $id
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function closeProductBatch($id){

        /** @var CProductBatch $productBatch */
        $productBatch = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$id]);

        if($productBatch->closingDate == 0) {
            $productBatch->closingDate = date('Y-m-d H:i:s');
            $productBatch->update();
        }

        return true;
    }

    public function calculateProductBatchCost($productBatch){

        if(is_numeric($productBatch)){
            /** @var CProductBatch $pB */
            $pB = $this->findOneBy(['id'=>$productBatch]);
        }

        $numberOfProducts = count($pB->getElements());

        $unitPrice = $pB->contractDetails->workPriceList->price;
        $cost = $unitPrice * $numberOfProducts;

        return $cost;

    }

    /**
     * @param $id
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function acceptProductBatch($id){

        $pB = $this->findOneBy(['id'=>$id]);

        if($pB->confirmationDate !=0){
            return false;
        }
        $date = new \DateTime();
        $pB->confirmationDate = date_format($date, 'Y-m-d H:i:s');
        $pB->update();

        return $pB->id;
    }

    /**
     * @param $productBatch
     * @param $scheduledDelivery
     * @param $value
     * @param $contractDetailsId
     * @return CProductBatch
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function associateProductBatch($productBatch, $scheduledDelivery, $value, $contractDetailsId){

        /** @var CContractDetails $contractDetails */
        $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBy(['id'=>$contractDetailsId]);

        $sectionalCodeId = $contractDetails->workCategory->sectionalCodeId;

        /** @var CSectionalRepo $sectionalRepo */
        $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');

        /** @var CProductBatch $productBatch */
        $productBatch->scheduledDelivery = $scheduledDelivery;
        $productBatch->value = $value;
        $productBatch->contractDetailsId = $contractDetailsId;
        $productBatch->sectional = $sectionalRepo->createNewSectionalCode($sectionalCodeId);
        $productBatch->update();

        /** @var CWorkCategoryStepsRepo $catStR */
        $catStR = \Monkey::app()->repoFactory->create('WorkCategorySteps');

        $catId = $productBatch->contractDetails->workCategory->id;

        /** @var CWorkCategorySteps $initStep */
        $initStep = $catStR->getFirstStepsFromCategoryId($catId);

        /** @var CObjectCollection $elems */
        $elems = $productBatch->getElements();

        foreach ($elems as $elem){
            $elem->workCategoryStepsId = $initStep->id;
            $elem->update();
        }

        return $productBatch;
    }


    public function checkRightLanguage($pbId, $langId){

        /** @var CProductBatch $pb */
        $pb = $this->findOneBy(['id'=>$pbId]);

        if(is_null($pb)) return false;



        if(is_null($pb->contractDetailsId)){
            $wk = $pb->workCategoryId;
        } else {
            $wk = $pb->contractDetails->workCategory->id;
        }

        $correct = false;
        switch ($langId){
            case 2:
                if($wk == CWorkCategory::NAME_ENG) $correct = true;
                break;
            case 3:
                if($wk == CWorkCategory::NAME_DTC) $correct = true;
                break;
        }

        return $correct;
    }

}