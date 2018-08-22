<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;


/**
 * Class CContractsListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/03/2018
 * @since 1.0
 */
class CContractsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        /** @var CUser $user */
        $user = \Monkey::app()->getUser();
        $userId = $user->id;

        $allShop = $user->hasPermission('allShops');
        $isWorker = $user->hasPermission('worker');



        $sql = "
            SELECT C.id,
                  C.name as contractName,
                  C.description as contractDescription,
                  F.name as foisonName,
                  F.surname as foisonSurname,
                  F.email as foisonEmail,
                  C.accepted,
                  C.acceptedDate,
                  F.userId,
                  C.isActive
            FROM Foison F
            JOIN Contracts C ON F.id = C.foisonId
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        if($isWorker && !$allShop) {
            $datatable->addCondition('userId', [$userId]);
        }


        $datatable->doAllTheThings(false);

        /** @var CContractsRepo $contractsRepo */
        $contractsRepo = \Monkey::app()->repoFactory->create('Contracts');

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $url = $blueseal."work/contratti/";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CContracts $contracts */
            $contracts = $contractsRepo->findOneBy(['id'=>$row["id"]]);
            $row["row_id"] = $contracts->id;
            $row["id"] = "<a href='".$url.$row["id"]."' target='_blank'>".$contracts->id."</a>";
            $row["contractName"] = $contracts->name;
            $row["contractDescription"] = $contracts->description;
            $row["foisonName"] = $contracts->foison->name;
            $row["foisonSurname"] = $contracts->foison->surname;
            $row["foisonEmail"] = $contracts->foison->email;
            $row["accepted"] = ($contracts->accepted == 1 ? "SI" : "NO");
            $row["acceptedDate"] = $contracts->acceptedDate;
            $row["isActive"] = $contracts->isActive == 0 ? "Non attivo" : "Attivo";

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}