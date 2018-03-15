<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CContracts;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;


/**
 * Class CContractDetailsListAjaxController
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
class CContractDetailsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $idContract = $this->data["idcontract"];

        $sql = "
            SELECT cd.id,
                  wk.nome as categoryName,
                  wpl.nome as priceListName,
                  c.name as contractName
            FROM ContractDetails cd
            JOIN WorkCategory wk ON cd.workCategoryId = wk.id
            JOIN WorkPriceList wpl ON cd.workPriceListId = wpl.id
            JOIN Contracts c ON cd.contractId = c.id
            WHERE c.id = $idContract
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CContractsRepo $contractsRepo */
        $contractsRepo = \Monkey::app()->repoFactory->create('Contracts');

        //$blueseal = $this->app->baseUrl(false).'/blueseal/';
        //$url = $blueseal."prodotti/gruppo-taglie/aggiungi";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CContracts $contracts */
            $contracts = $contractsRepo->findOneBy(['id'=>$row["id"]]);
            $row["id"] = $contracts->id;
            $row["contractName"] = $contracts->name;
            $row["contractDescription"] = $contracts->description;
            $row["foisonName"] = $contracts->foison->name;
            $row["foisonSurname"] = $contracts->foison->surname;
            $row["foisonEmail"] = $contracts->foison->email;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}