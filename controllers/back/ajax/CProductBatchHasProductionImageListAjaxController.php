<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductionImage;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;


/**
 * Class CProductBatchDetailsListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/03/2018
 * @since 1.0
 */
class CProductBatchHasProductionImageListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $productBatchId = $this->data["productbatchid"];

        $sql = "
            SELECT pbd.id as id,
                 pbd.imageName as imageName,
                 pbd.productBatchId as productBatchId,
                 `wcs`.`name` as stepName,
                 pbd.shopId as shopId,
                 s.`name` as shopName  
           FROM ProductBatchHasProductionImage pbd
           JOIN Shop s on pbd.shopId=s.id
           LEFT JOIN WorkCategorySteps wcs ON pbd.workCategoryStepsId = wcs.id
        
           WHERE pbd.productBatchId = $productBatchId
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);


        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductionImage');
        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductBatchHasProductionImage $pbr */
            $pbr = $pbdRepo->findOneBy(['id'=>$row["id"]]);



            $row["DT_RowId"] = $pbr->id;
            $row['DT_imageName']=$pbr->imageName;
            $row['imageName']=$pbr->imageName;
            $shop=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$pbr->shopId]);
            $row['shopName']=$shop->name;
            $row['shopId']=$pbr->shopId;

            $row["work_category"] = $pbr->productBatch->contractDetails->workCategory->id;
            $row["id"] = $pbr->id;
            //$row["productCode"] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $product->id . '&productVariantId=' . $product->productVariantId . '">' . $product->id . '-' . $product->productVariantId . '</a>';


            if(is_null($pbr->workCategoryStepsId)){
                $stepName = '-';
            } else {
                $stepName = $pbr->workCategorySteps->name;
            }


            $row["stepName"] = $stepName;

            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="/media/folderImages/' . $pbr->imageName . '" /></a>';



            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}