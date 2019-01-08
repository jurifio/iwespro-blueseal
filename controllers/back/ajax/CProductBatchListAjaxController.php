<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductBrand;
use bamboo\domain\entities\CProductBatchHasProductDetail;
use bamboo\domain\entities\CProductBatchHasProductName;
use bamboo\domain\entities\CProductCategory;
use bamboo\domain\entities\CProductName;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductNameRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;


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
                  pb.description as descr,
                  pb.name as name,
                  pb.workCategoryId,
                  pb.marketplace,
                  pb.requestClosingDate,
                  pb.isUnassigned,
                  pb.operatorRankIwes,
                  pb.timingRank,
                  pb.qualityRank,
                  pb.tolleranceDelivery,
                  pb.estimatedWorkDays
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

        /** @var CWorkCategoryRepo $wCRepo */
        $wCRepo = \Monkey::app()->repoFactory->create('WorkCategory');

        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $url = $blueseal."work/lotti/";

        /** @var CProductBatchRepo $pbrRepo */
        $pbrRepo = \Monkey::app()->repoFactory->create('ProductBatch');

        /** @var CProductNameRepo $pNameRepo */
        $pNameRepo = \Monkey::app()->repoFactory->create('ProductName');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $finish = 0;
            $todo = 0;

            /** @var CProductBatch $pbr */
            $pbr = $pbrRepo->findOneBy(['id'=>$row["id"]]);

            if($pbr->unfitDate != 0 && $pbr->closingDate == 0 && $pbr->isFixed == 0 && $pbr->isUnassigned == 0){
                $row["DT_RowClass"] = "red";
            } else if($pbr->unfitDate != 0 && $pbr->closingDate == 0 && $pbr->isFixed == 1){
                $row["DT_RowClass"] = "green";
            } else if($pbr->isUnassigned == 1 && $pbr->unfitDate == 0) {
                $row["DT_RowClass"] = "brown";
            } else if($pbr->isUnassigned == 1 && $pbr->unfitDate != 0){
                $row["DT_RowClass"] = "brown text-red";

            }

            $row["row_id"] = $pbr->id;

            $row["id"] = (((is_null($pbr->confirmationDate) && !$allShop) || (is_null($pbr->contractDetailsId))) ? $pbr->id :'<a href="'.$url.$pbr->contractDetails->workCategory->slug.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>');

            if((!is_null($pbr->closingDate) && !$allShop) || ((is_null($pbr->confirmationDate) && !$allShop))) {
                $row["id"] = $pbr->id;
            } else if(((is_null($pbr->confirmationDate) && !$allShop))){
                $row["id"] = $pbr->id;
            } else if(is_null($pbr->contractDetailsId)) {

                switch ($pbr->workCategoryId){
                    case CWorkCategory::NORM:
                        $row["id"] = '<a href="'.$url.CWorkCategory::SLUG_EMPTY_NORM.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
                        break;
                    case CWorkCategory::BRAND:
                        $row["id"] = '<a href="'.$url.CWorkCategory::SLUG_EMPTY_BRAND.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
                        break;
                    case CWorkCategory::NAME_ENG:
                    case CWorkCategory::NAME_DTC:
                        $row["id"] = '<a href="'.$url.CWorkCategory::SLUG_EMPTY_TRANS.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
                        break;
                    case CWorkCategory::TXT_FAS:
                    case CWorkCategory::TXT_FAS_BLOG:
                    case CWorkCategory::TXT_INFL:
                    case CWorkCategory::TXT_PRT:
                    case CWorkCategory::TXT_BRAND:
                    case CWorkCategory::TXT_FB:
                        $row["id"] = '<a href="'.$url.$pbr->workCategory->slug.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
                        break;
                    case CWorkCategory::DET_ENG:
                        $row["id"] = '<a href="'.$blueseal.$pbr->workCategory->slug . '/' . CProductBatchHasProductDetail::LANG_ENG . '?pbId='.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
                        break;
                    case CWorkCategory::DET_DTC:
                        $row["id"] = '<a href="'.$blueseal.$pbr->workCategory->slug . '/' . CProductBatchHasProductDetail::LANG_DTC . '?pbId='.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
                        break;
                }


            } else {
                if($pbr->contractDetails->workCategory->id == CWorkCategory::NAME_ENG || $pbr->contractDetails->workCategory->id == CWorkCategory::NAME_DTC) {

                    if($pbr->isUnassigned == 1 && $isWorker && !$allShop){
                        $row["id"] = $pbr->id;
                    } else {
                        /** @var CObjectCollection $pBatchNames */
                        $pBatchNames = $pbr->getElements();
                        $pLangId = $pBatchNames->getFirst()->langId;
                        $par = [];
                        /** @var CProductBatchHasProductName $pName */
                        foreach ($pBatchNames as $pName){

                            /** @var CProductName $pn */
                            $pn = $pNameRepo->findOneBy(['name'=>$pName->productName, 'langId'=>1]);

                            $par[] = $pn->id;
                        }
                        $parUrl = http_build_query($par, 'id_');


                        $row["id"] = '<a href="'.$blueseal.$pbr->contractDetails->workCategory->slug.'/'.$pLangId.'?' . 'pbId=' . $pbr->id . '&' .$parUrl.'" target="_blank">'.$pbr->id.'</a>';
                    }

                } else if ($pbr->contractDetails->workCategory->id == CWorkCategory::DET_ENG || $pbr->contractDetails->workCategory->id == CWorkCategory::DET_DTC) {
                    if($pbr->isUnassigned == 1 && $isWorker && !$allShop){
                        $row["id"] = $pbr->id;
                    } else {
                        /** @var CObjectCollection $pBatchDetails */
                        $pBatchDetails = $pbr->getElements();
                        $pLangId = $pBatchDetails->getFirst()->langId;

                        $row["id"] = '<a href="'.$blueseal.$pbr->contractDetails->workCategory->slug.'/'.$pLangId.'?' . 'pbId=' . $pbr->id . '" target="_blank">'.$pbr->id.'</a>';
                    }
                } else {
                    //$row["id"] = '<a href="'.$url.$pbr->contractDetails->workCategory->slug.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';

                    if($pbr->isUnassigned == 1 && $isWorker && !$allShop){
                        $row["id"] = $pbr->id;
                    } else {
                        $row["id"] = '<a href="'.$url.$pbr->contractDetails->workCategory->slug.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
                    }
                }

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



            $row["numberOfProduct"] = count($pbr->getElements());
            $row["documentId"] = $pbr->documentId;
            $row["foisonEmail"] = (is_null($pbr->contractDetailsId) ? 'Undefined' : $pbr->contractDetails->contracts->foison->email);
            $row["descr"] = $pbr->description;
            $row["name"] = $pbr->name;



            /** @var CObjectCollection $elems */
            $elems = $pbr->getElements();

            if(!is_null($pbr->contractDetailsId)) {
                foreach ($elems as $elem) {
                    if (!is_null($elem->workCategorySteps->rgt)) {
                        $todo++;
                    } else {
                        $finish++;
                    }
                }
            }

            $row['finish'] = $finish;
            $row['todo'] = $todo;

            if(is_null($pbr->contractDetailsId)){
                /** @var CWorkCategory $cat */
                $cat = $wCRepo->findOneBy(['id'=>$pbr->workCategoryId]);
            } else {
                /** @var CWorkCategory $cat */
                $cat = $pbr->contractDetails->workCategory;
            }


            $row['requestClosingDate'] = $pbr->requestClosingDate;
            $row['workCategoryId'] = $cat->name;
            $row['marketplace'] = $pbr->marketplace == 1 ? 'Visibile' : 'Nascosto';
            $row['operatorRankIwes'] = $pbr->operatorRankIwes;
            $row['timingRank'] = $pbr->timingRank;
            $row['tolleranceDelivery'] = $pbr->tolleranceDelivery;
            $row['qualityRank'] = $pbr->qualityRank;
            $row['estimatedWorkDays'] = $pbr->estimatedWorkDays;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}