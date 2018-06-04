<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductBrand;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\entities\CWorkCategorySteps;
use bamboo\domain\repositories\CContractDetailsRepo;
use bamboo\domain\repositories\CContractsRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CWorkCategoryRepo;
use bamboo\domain\repositories\CWorkCategoryStepsRepo;


/**
 * Class CProductBrandBatchDetailsListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/06/2018
 * @since 1.0
 */
class CProductBrandBatchDetailsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $productBatchId = $this->data["productbatchid"];

        $sql = "
            SELECT 
              pBatch.id,
              pBrand.id as pBrandId,
              pBrand.slug,
              pBrand.name,
              pBrand.description,
              pBrand.logoUrl,
              wcs.name as stepName
            FROM ProductBatch pBatch
            JOIN ProductBatchHasProductBrand bb ON pBatch.id = bb.productBatchId
            JOIN ProductBrand pBrand ON bb.productBrandId = pBrand.id
            JOIN WorkCategorySteps wcs ON bb.workCategoryStepsId = wcs.id
            WHERE pBatch.id = $productBatchId
        ";

        $datatable = new CDataTables($sql, ['id','pBrandId'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CProductBatchRepo $pBatchRepo */
        $pBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');


        /** @var CProductBatch $productBatch */
        $productBatch = $pBatchRepo->findOneBy(['id'=>$productBatchId]);

        /** @var CObjectCollection $pBrands */
        $pBrands = $productBatch->productBrand;

        $url = $this->app->baseUrl(false) . '/blueseal/prodotti/brand/modifica?id=';

        /** @var CWorkCategoryStepsRepo $wcsRepo */
        $wcsRepo = \Monkey::app()->repoFactory->create('WorkCategorySteps');

        foreach ($datatable->getResponseSetData() as $key=>$row) {


            /** @var CProductBrand $brand */
            $brand = $pBrands->findOneByKey('id', $row['pBrandId']);

            $row['id'] = $productBatch->id;
            $row['DT_RowId'] = $brand->id;
            $row['pBrandId'] = "<a href='".$url.$brand->id."' target='_blank'>".$brand->id."</a>";
            $row['slug'] = $brand->slug;
            $row['name'] = $brand->name;
            $row['description'] = $brand->description;
            $row['logoUrl'] = $brand->logoUrl;

            //$stepId = $productBatch->productBatchHasProductBrand->findOneByKey('productBrandId', $brand->id)->workCategoryStepsId;

            /** @var CProductBatchHasProductBrand $pbhpb */
            $pbhpb = $productBatch->productBatchHasProductBrand->findOneByKey('productBrandId', $brand->id);

            if(is_null($pbhpb->workCategoryStepsId)){
                $stepName = '-';
            } else if($pbhpb->workCategoryStepsId == CProductBatchHasProductBrand::UNFIT_BRAND && $pbhpb->productBatch->unfitDate == 0){
                $stepName = '<p style="color: red; font-weight: bold">'.$pbhpb->workCategorySteps->name.' IN VERIFICA, NON MODIFICARE!</p>';
            } else if($pbhpb->workCategoryStepsId == CProductBatchHasProductBrand::UNFIT_BRAND){
                $stepName = '<p style="color: red; font-weight: bold">'.$pbhpb->workCategorySteps->name.' DA MODIFICARE</p>';
            } else {
                $stepName = $pbhpb->workCategorySteps->name;
            }


            $row["stepName"] = $stepName;

           // $row['stepName'] = $wcsRepo->findOneBy(['id'=>$stepId])->name;
            $row['work_category'] = $productBatch->contractDetails->workCategory->id;

            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}