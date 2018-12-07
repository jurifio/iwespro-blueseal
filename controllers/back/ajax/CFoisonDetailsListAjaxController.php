<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CFoisonHasInterest;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CUserAddress;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CFoisonRepo;


/**
 * Class CFoisonListAjaxController
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
class CFoisonDetailsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        $foisonId = \Monkey::app()->router->request()->getRequestData('foisonid');
        $user = \Monkey::app()->getUser();


        $allShop = $user->hasPermission('allShops');
        $isWorker = $user->hasPermission('worker');

        $sql = "
            SELECT pb.id,
                  wk.name as workCategory,
                  pb.description as descr,
                  pb.name as name,
                  pb.operatorRankIwes,
                  pb.timingRank,
                  pb.qualityRank
            FROM ProductBatch pb
            LEFT JOIN ContractDetails cd ON pb.contractDetailsId = cd.id
            LEFT JOIN WorkCategory wk ON cd.workCategoryId = wk.id
            LEFT JOIN Contracts c ON cd.contractId = c.id
            LEFT JOIN Foison f ON c.foisonId = f.id
            WHERE f.id = $foisonId
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

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
                }


            } else {
                if($pbr->contractDetails->workCategory->id != CWorkCategory::NAME_ENG && $pbr->contractDetails->workCategory->id != CWorkCategory::NAME_DTC ){
                    //$row["id"] = '<a href="'.$url.$pbr->contractDetails->workCategory->slug.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';

                    if($pbr->isUnassigned == 1 && $isWorker && !$allShop){
                        $row["id"] = $pbr->id;
                    } else {
                        $row["id"] = '<a href="'.$url.$pbr->contractDetails->workCategory->slug.'/'.$pbr->id.'" target="_blank">'.$pbr->id.'</a>';
                    }
                } else if($pbr->contractDetails->workCategory->id == CWorkCategory::NAME_ENG || $pbr->contractDetails->workCategory->id == CWorkCategory::NAME_DTC) {

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


                }

            }







            $row["descr"] = $pbr->description;
            $row["name"] = $pbr->name;


            if(is_null($pbr->contractDetailsId)){
                /** @var CWorkCategory $cat */
                $cat = $wCRepo->findOneBy(['id'=>$pbr->workCategoryId]);
            } else {
                /** @var CWorkCategory $cat */
                $cat = $pbr->contractDetails->workCategory;
            }


            $row['workCategory'] = $cat->name;

            $row['operatorRankIwes'] = $pbr->operatorRankIwes;
            $row['timingRank'] = $pbr->timingRank;
            $row['qualityRank'] = $pbr->qualityRank;


            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}