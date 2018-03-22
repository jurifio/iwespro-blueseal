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
     * @param $value
     * @param $contractDetailsId
     * @param $products
     * @return bool
     */
    public function createNewProductBatch($scheduledDelivery, $value, $contractDetailsId, $products){

        try {
            /** @var CContractDetails $contractDetails */
            $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBY(['id'=>$contractDetailsId]);

            $sectionalCode = $contractDetails->workCategory->sectionalCode;

            /** @var CSectionalRepo $sectionalRepo */
            $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');

            /** @var CProductBatch $productBatch */
            $productBatch = $this->getEmptyEntity();
            $productBatch->scheduledDelivery = $scheduledDelivery;
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

        $numberOfProducts = count($pB->productBatchDetails);

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

        return true;
    }
}