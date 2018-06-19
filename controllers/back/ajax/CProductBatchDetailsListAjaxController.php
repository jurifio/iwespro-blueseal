<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
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
class CProductBatchDetailsListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $productBatchId = $this->data["productbatchid"];

        $sql = "
            SELECT pbd.id,
                  concat(pbd.productId,'-',pbd.productVariantId) as productCode,
                  pbd.productId,
                  pbd.productVariantId,
                  wcs.name as stepName,
                  pbd.note,
                  pb.name as brand,
                  concat(pse.name, ' ', pse.year) AS season,
                  pcg.name AS colorGroup,
                  pv.description AS colorNameManufacturer,
                  psiz.name AS stock
            FROM ProductBatchDetails pbd
            JOIN Product p ON pbd.productVariantId = p.productVariantId AND p.id = pbd.productId
            JOIN ProductBrand pb ON p.productBrandId = pb.id
            JOIN ProductSeason pse ON p.productSeasonId = pse.id
            JOIN ProductVariant pv ON p.productVariantId = pv.id
            LEFT JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
            LEFT JOIN WorkCategorySteps wcs ON pbd.workCategoryStepsId = wcs.id
            LEFT JOIN (ProductSku psk
                JOIN ProductSize psiz ON psk.productSizeId = psiz.id)
                ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)
            WHERE pbd.productBatchId = $productBatchId
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);


        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');
        $modifica = $this->app->baseUrl(false) . "/blueseal/friend/prodotti/modifica";
        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductBatchDetails $pbr */
            $pbr = $pbdRepo->findOneBy(['id'=>$row["id"]]);

            /** @var CProduct $product */
            $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$pbr->productId, 'productVariantId'=>$pbr->productVariantId]);

            $row["DT_RowId"] = $product->printId();
            $row["work_category"] = $pbr->productBatch->contractDetails->workCategory->id;
            $row["id"] = $pbr->id;
            //$row["productCode"] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $product->id . '&productVariantId=' . $product->productVariantId . '">' . $product->id . '-' . $product->productVariantId . '</a>';
            $row["productCode"] = $pbr->productId.'-'.$pbr->productVariantId;

            if(is_null($pbr->workCategoryStepsId)){
                $stepName = '-';
            } else if($pbr->workCategoryStepsId == CProductBatchDetails::UNFIT_NORM && $pbr->productBatch->unfitDate == 0){
                $stepName = '<p style="color: red; font-weight: bold">'.$pbr->workCategorySteps->name.' IN VERIFICA, NON MODIFICARE!</p>';
            } else if($pbr->workCategoryStepsId == CProductBatchDetails::UNFIT_NORM){
                $stepName = '<p style="color: red; font-weight: bold">'.$pbr->workCategorySteps->name.' DA MODIFICARE</p>';
            } else {
                $stepName = $pbr->workCategorySteps->name;
            }


            $row["stepName"] = $stepName;
            $row['colorGroup'] = '<span class="small">' . (!is_null($product->productColorGroup) ? $product->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['colorNameManufacturer'] = $product->productVariant->description;
            $row['season'] = '<span class="small">' . $product->productSeason->name . " " . $product->productSeason->year . '</span>';
            $row['details'] = "";
            foreach ($product->productSheetActual as $k => $v) {
                if ($trans = $v->productDetail->productDetailTranslation->getFirst()) {
                    $row['details'] .= '<span class="small">' . $trans->name . "</span><br />";
                }
            }

            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $product->getDummyPictureUrl() . '" /></a>';
            $row['brand'] = isset($product->productBrand) ? $product->productBrand->name : "";
            $row['productName'] = $product->productNameTranslation->getFirst() ? $product->productNameTranslation->getFirst()->name : "";
            $row['description'] = '<span class="small">' . ($product->productDescriptionTranslation->getFirst() ? $product->productDescriptionTranslation->getFirst()->description : "") . '</span>';
            $row['categoryId'] = '<span class="small">' . $product->getLocalizedProductCategories(" ", "<br>") . '</span>';
            $row['productCard'] = (!$product->getProductCardUrl() ? '-' :'<a href="#1" class="enlarge-your-img"><img width="50" src="' . $product->getProductCardUrl() . '" /></a>');
            $row['row_pCardUrl'] = (!$product->getProductCardUrl() ? '-' : $product->getProductCardUrl());
            $row['note'] = $pbr->note;
            $row['row_dummyUrl'] = $product->getDummyPictureUrl();
            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$product->printId().'"></table>';


            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}