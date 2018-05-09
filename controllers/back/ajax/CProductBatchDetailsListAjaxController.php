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
                  wcs.name as stepName
            FROM ProductBatchDetails pbd
            JOIN WorkCategorySteps wcs ON pbd.workCategoryStepsId = wcs.id
            WHERE pbd.productBatchId = $productBatchId
        ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);


        /** @var CProductBatchDetailsRepo $pbdRepo */
        $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CProductBatchDetails $pbr */
            $pbr = $pbdRepo->findOneBy(['id'=>$row["id"]]);

            /** @var CProduct $product */
            $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$pbr->productId, 'productVariantId'=>$pbr->productVariantId]);

            $row["DT_RowId"] = $product->printId();
            $row["id"] = $pbr->id;
            $row["productCode"] = $pbr->productId.'-'.$pbr->productVariantId;
            $row["stepName"] = $pbr->workCategorySteps->name;
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
            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}