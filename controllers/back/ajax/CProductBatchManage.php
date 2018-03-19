<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CProductBatchManage
 * @package bamboo\controllers\back\ajax
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
class CProductBatchManage extends AAjaxController
{
    /**
     *
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $products = $data["products"];
        $foisonId = $data["foisonId"];
        $contractDetailsId = $data["contractDetailsId"];
        $deliveryDate = $data["deliveryDate"];
        $closingDate = $data["closingDate"];
        $numberOfProduct = $data["numberOfProduct"];

        //Costo
        /** @var CContractDetails $contractDetails */
        $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBy(['id'=>$contractDetailsId]);;
        $unitPrice = $contractDetails->workPriceList->price;
        $value = $unitPrice * $numberOfProduct;

        /** @var CProductBatchRepo $productBatchRepo */
        $productBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');
        if($productBatchRepo->createNewProductBatch($deliveryDate, $closingDate, $value, $contractDetailsId, $products)){
            $res = "Lotto creato con sucecsso";
        } else {
            $res = "Errore durante la creazione";
        }

        return $res;
    }

    /**
     * @return string
     */
    public function get(){
        $contractDetailId = \Monkey::app()->router->request()->getRequestData('contractDetail');
        $numberOfProduct = \Monkey::app()->router->request()->getRequestData('numberOfProduct');

        /** @var CContractDetailsRepo $contractDetailRepo */
        $contractDetailRepo = \Monkey::app()->repoFactory->create('ContractDetails');
        /** @var CContractDetails $contractDetails */
        $contractDetails = $contractDetailRepo->findOneBy(['id'=>$contractDetailId]);

        //Costo stimato
        $unitPrice = $contractDetails->workPriceList->price;
        $cost = $unitPrice * $numberOfProduct;

        //Lotto sezionale stimato
        /** @var CSectionalRepo $sectionalRepo */
        $sectionalCode = $contractDetails->workCategory->sectionalCode;

        $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');
        $sectional = $sectionalRepo->calculateNextSectionalNumber($sectionalCode);

        $result = [];
        $result["cost"] = $cost;
        $result["sectional"] = $sectional;

        return json_encode($result);
    }

}