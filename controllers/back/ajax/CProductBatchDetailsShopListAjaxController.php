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
class CProductBatchDetailsShopListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $user = \Monkey::app()->getUser();
        $isWorker = $user->hasPermission('worker');
        $allShops = $user->hasPermission('allShops');
        $isShopUser = $user->hasPermission('/admin/product/edit');
        $userHasShopRepo=\Monkey::app()->repoFactory->create('UserHasShop');
        $user = \Monkey::app()->getUser()->id;
        $shopId='all';
        if($allShops){
            $shopId='all';
        }else{
            $userHasShop=$userHasShopRepo->findOneBy(['userId'=>$user]);
            $shopId=$userHasShop->userId;
        }


if ($shopId!='all'){
    $sqlFilter='and SHP.shopId='.$shopId;
}else {
    $sqlFilter = '';
}
        $sql = "
            SELECT 
                 p.id,
                 p.id as productId,  
                 p.productVariantId,
                 pb.name as brand,
                 concat(pse.name, ' ', pse.year) AS season,
                 pcg.name AS colorGroup,
                 pv.description AS colorNameManufacturer,
                 pc.id  AS categoryId
           FROM  Product p 
           JOIN ProductBrand pb ON p.productBrandId = pb.id
           JOIN ProductSeason pse ON p.productSeasonId = pse.id
           JOIN ProductVariant pv ON p.productVariantId = pv.id
           JOIN ShopHasProduct SHP on p.id = SHP.productId and p.productVariantId = SHP.productVariantId   
           LEFT JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
           LEFT JOIN (ProductHasProductCategory ppc
                         JOIN ProductCategory pc ON ppc.productCategoryId = pc.id
               ) ON (p.id, p.productVariantId) = (ppc.productId,ppc.productVariantId)
           WHERE 1=1 ".$sqlFilter ;

        $datatable = new CDataTables($sql,  ['id', 'productVariantId'], $_GET, true);

        $datatable->doAllTheThings();


        $modifica = $this->app->baseUrl(false) . "/blueseal/friend/prodotti/modifica";
        foreach ($datatable->getResponseSetData() as $key=>$row) {



            /** @var CProduct $product */
            $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$row["id"], 'productVariantId'=>$row['productVariantId']]);

            $row["DT_RowId"] = $product->printId();

            $row["productId"] = $product->id;
            $row["productVariantId"] = $product->productVariantId;

            $row["id"] = $product->printId();
            //$row["productCode"] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $product->id . '&productVariantId=' . $product->productVariantId . '">' . $product->id . '-' . $product->productVariantId . '</a>';
            $row["productCode"] = $product->id.'-'.$product->productVariantId;




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
            $row['row_dummyUrl'] = $product->getDummyPictureUrl();
            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$product->printId().'"></table>';


            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}