<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;


/**
 * Class CProductBatchListAjaxController
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
class CProductBatchListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        $user = \Monkey::app()->getUser();
        $userId = $user->id;

        $allShop = $user->hasPermission('allShops');
        $isWorker = $user->hasPermission('worker');

        $sql = "
            SELECT pb.id,
                  pb.creationDate,
                  pb.scheduledDelivery,
                  pb.confirmationDate,
                  pb.closingDate,
                  pb.unfitDate,
                  pb.value,
                  pb.paid,
                  pb.sectional,
                  concat(f.name,' ',f.surname) as foison,
                  f.userId,
                  wk.name as workCategory,
                  pb.documentId,
                  pb.description as descr
            FROM ProductBatch pb
            LEFT JOIN ContractDetails cd ON pb.contractDetailsId = cd.id
            LEFT JOIN WorkCategory wk ON cd.workCategoryId = wk.id
            LEFT JOIN Contracts c ON cd.contractId = c.id
            LEFT JOIN Foison f ON c.foisonId = f.id
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        if($isWorker && !$allShop) {
            $datatable->addCondition('userId', [$userId]);
        }

        $datatable->doAllTheThings(false);


        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $url = $blueseal."work/lotti/";

        /** @var CProductBatchRepo $pbrRepo */
        $pbrRepo = \Monkey::app()->repoFactory->create('ProductBatch');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductBatch $pbr */
            $pbr = $pbrRepo->findOneBy(['id'=>$row["id"]]);

            if($pbr->unfitDate != 0 && $pbr->closingDate == 0 && $pbr->isFixed == 0){
                $row["DT_RowClass"] = "red";
            } else if($pbr->unfitDate != 0 && $pbr->closingDate == 0 && $pbr->isFixed == 1){
                $row["DT_RowClass"] = "green";
            }

            $row["row_id"] = $pbr->id;

            $row["id"] = (((is_null($pbr->confirmationDate) && !$allShop) || (is_null($pbr->contractDetailsId))) ? $pbr->id :'<a href="'.$url.$pbr->contractDetails->workCategory->slug.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>');

            if(((is_null($pbr->confirmationDate) && !$allShop) )){
                $row["id"] = $pbr->id;
            } else if(is_null($pbr->contractDetailsId)) {
                $row["id"] = '<a href="'.$url.'prodotti'.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
            } else {
                $row["id"] = '<a href="'.$url.$pbr->contractDetails->workCategory->slug.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
            }


            $row["creationDate"] = $pbr->creationDate;
            $row["scheduledDelivery"] = $pbr->scheduledDelivery;
            $row["confirmationDate"] = ($pbr->confirmationDate == 0 ? "-" : $pbr->confirmationDate);
            $row["closingDate"] = ($pbr->closingDate == 0 ? "-" : $pbr->closingDate);
            $row["unfitDate"] = ($pbr->unfitDate== 0 ? "-" : $pbr->unfitDate);
            $row["value"] = $pbr->value;
            $row["paid"] = ($pbr->paid == 1 ? "yes" : "no");
            $row["sectional"] = $pbr->sectional;
            $row["foison"] =(is_null($pbr->contractDetailsId) ? 'Undefined' : $pbr->contractDetails->contracts->foison->name.' '.$pbr->contractDetails->contracts->foison->surname);
            $row["numberOfProduct"] = count($pbr->productBatchDetails);
            $row["workCategory"] = (is_null($pbr->contractDetailsId) ? 'Undefined' : $pbr->contractDetails->workCategory->name);
            $row["documentId"] = $pbr->documentId;
            $row["foisonEmail"] = (is_null($pbr->contractDetailsId) ? 'Undefined' : $pbr->contractDetails->contracts->foison->email);
            $row["descr"] = $pbr->description;

            /** @var CObjectCollection $pbDetails */
            $pbDetails = $pbr->productBatchDetails;

            $finish = 0;
            $todo = 0;
           if(!is_null($pbr->contractDetailsId)) {
                /** @var CProductBatchDetails $singleProductBatchDetails */
                foreach ($pbDetails as $singleProductBatchDetails) {
                    if (!is_null($singleProductBatchDetails->workCategorySteps->rgt)) {
                        $todo++;
                    } else {
                        $finish++;
                    }
                }
            }
            $row['finish'] = $finish;
            $row['todo'] = $todo;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}