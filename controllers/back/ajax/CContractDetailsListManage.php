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
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CContractDetailsListManage
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
class CContractDetailsListManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $workPriceListType = $data["workPriceListType"];
        $workCategoryId = $data["workCategoryId"];
        $workPriceListId = $data["workPriceListId"];
        $contractId = $data["contractId"];
        $contractDetailName = $data["contractDetailName"];
        $qty = $data["qty"];
        $note = $data["note"];

        if(empty($workCategoryId) || empty($workPriceListId) || empty($contractId) || empty($contractDetailName)){
            $res = "Inserisci tutti i dati";
            return $res;
        }

        /** @var CContractDetailsRepo $contractDetailRepo */
        $contractDetailRepo = \Monkey::app()->repoFactory->create('ContractDetails');

        if($contractDetailRepo->createNewContractDetail($workPriceListType,$contractId, $workCategoryId, $workPriceListId, $contractDetailName, $qty, $note)){
            $res = "Contratto creato con successo";
        };

        return $res;
    }

}