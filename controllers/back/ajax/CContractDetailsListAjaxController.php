<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\repositories\CContractDetailsRepo;
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
                  cd.contractDetailName as contractDetailName,
                  wk.name as categoryName,
                  wpl.name as priceListName,
                  c.name as contractName,
                  cd.dailyQty,
                  cd.note
  
            FROM ContractDetails cd
            JOIN WorkCategory wk ON cd.workCategoryId = wk.id
            JOIN WorkPriceList wpl ON cd.workPriceListId = wpl.id
            JOIN Contracts c ON cd.contractId = c.id
            WHERE c.id = $idContract
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CContractDetailsRepo $contractDetailsRepo */
        $contractDetailsRepo = \Monkey::app()->repoFactory->create('ContractDetails');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CContractDetails $contractDetails */
            $contractDetails = $contractDetailsRepo->findOneBy(['id'=>$row["id"]]);

            $row["id"] = $contractDetails->id;
            $row["contractDetailName"] = $contractDetails->contractDetailName;
            $row["categoryName"] = $contractDetails->workCategory->name;
            $row["contractName"] = $contractDetails->contracts->name;
            $row["priceListName"] = $contractDetails->workPriceList->name;
            $row["dailyQty"] = ($contractDetails->dailyQty == 0 ? "Non definita" : $contractDetails->dailyQty);
            $row["note"] = $contractDetails->note;
            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}