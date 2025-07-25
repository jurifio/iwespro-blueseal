<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CProductCatalogListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/12/2018
 * @since 1.0
 */
class CProductCatalogListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {
        $fields = \Monkey::app()->router->request()->getRequestData('fields');

            $sqlSelect = "
            SELECT 
              p.id,
              p.productVariantId,
              concat(s.id,'-',s.name) AS Shop,
              pv.description                                                                                    AS colorNameManufacturer,
              pcg.name                                                                                          AS colorGroup,
              concat(p.itemno, ' # ', pv.name)                                                                  AS cpf,
              ps.name AS productStatus,
              if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummyPicture,
              p.isOnSale                                                                                        AS isOnSale,
              pb.name AS productBrand,
              p.qty                                                                                             AS hasQty,
              pc.id as categoryId";

            $sqlFrom = "FROM Product p
              
              JOIN ProductStatus ps ON p.productStatusId = ps.id
              JOIN ProductBrand pb ON p.productBrandId = pb.id
              
              JOIN ProductVariant pv ON p.productVariantId = pv.id
              LEFT JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
              JOIN ShopHasProduct sp
                ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
              JOIN Shop s ON s.id = sp.shopId
              LEFT JOIN (ProductHasProductCategory ppc
                              JOIN ProductCategory pc ON ppc.productCategoryId = pc.id
                    ) ON (p.id, p.productVariantId) = (ppc.productId,ppc.productVariantId)";

            if(!empty($fields)) {
                foreach ($fields as $field) {
                    switch ($field) {
                        case 'stock':
                            $sqlSelect .= ', psiz.name AS stock';
                            $sqlFrom .= ' LEFT JOIN (ProductSku psk
                                         JOIN ProductSize psiz ON psk.productSizeId = psiz.id)
                                         ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)';
                            break;
                        case 'externalId':
                            $sqlSelect .= ', concat(ifnull(p.externalId, \'\'), \'-\', ifnull(dp.extId, \'\'), \'-\', ifnull(ds.extSkuId, \'\')) AS externalId';
                            $sqlFrom .= ' LEFT JOIN (DirtyProduct dp
                                         JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
                                         ON (sp.productId,sp.productVariantId,sp.shopId) = (dp.productId,dp.productVariantId,dp.shopId)';
                            break;
                        case 'season':
                            $sqlSelect .= ', concat(pse.name, \' \', pse.year) AS season';
                            $sqlFrom .= ' JOIN ProductSeason pse ON p.productSeasonId = pse.id';
                            break;
                        case 'hasDetails':
                            $sqlSelect .= ', if(((SELECT count(0)
                       FROM ProductSheetActual
                       WHERE ((ProductSheetActual.productId = p.id) AND
                              (ProductSheetActual.productVariantId = p.productVariantId))) > 2), \'sì\', \'no\')    AS hasDetails';
                            break;
                    }
                }
            }

         $sql = $sqlSelect . ' ' . $sqlFrom;

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CProductRepo $productRepo */
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        foreach ($datatable->getResponseSetData() as $key => $row) {

            /** @var CProduct $product */
            $product = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $product->printId();
            $row['productId'] = $product->id;
            $row['productVariantId'] = $product->productVariantId;
            $row['productStatus'] = $product->productStatus->name;
            $row['dummyPicture'] = '<img width="50" src="' . $product->getDummyPictureUrl() . '" />';
            $row['productBrand'] = $product->productBrand->name;

            $row['isOnSale'] = $product->isOnSale();

            $qty = 0;
            foreach ($product->productSku as $sku) {
                $qty += $sku->stockQty;
            }

            $row['hasQty'] = $qty;

            $row['friendPrices'] = [];
            $row['friendValues'] = [];
            $row['friendSalePrices'] = [];
            foreach ($product->shopHasProduct as $shp) {

                if(is_null($shp->salePrice)){
                    $of = 'null';
                } else if ($shp->salePrice == 0){
                    $of = '100%';
                } else {
                    $of = round(100 - (100 * $shp->salePrice / $shp->price), 2) . '%';
                }

                $row['friendPrices'][] = $shp->price;
                $row['friendSalePrices'][] = $shp->salePrice . '(' . $of . ')';
            }

            $row['friendPrices'] = implode('<br />',$row['friendPrices']);
            $row['friendSalePrices'] = implode('<br />',$row['friendSalePrices']);

            $row['cpf'] = '<span class="small">';
            $row['cpf'] .= $product->itemno . ' # ' . $product->productVariant->name;
            $row['cpf'] .= '</span>';

            $row['productName'] = $product->productNameTranslation->getFirst() ? $product->productNameTranslation->getFirst()->name : "";
            $row['colorNameManufacturer'] = $product->productVariant->description;
            $row['colorGroup'] = '<span class="small">' . (!is_null($product->productColorGroup) ? $product->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['shop'] = '<span class="small">' . $product->getShops('<br />', true) . '</span>';
            $row['categoryId'] = '<span class="small">' . $product->getLocalizedProductCategories(" ", "<br>") . '</span>';


            if(!empty($fields)) {
                foreach ($fields as $field) {
                    switch ($field) {
                        case 'stock':
                            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="' . $product->printId() . '"></table>';
                            break;
                        case 'externalId':
                            $row['externalId'] = '<span class="small">' . $product->getShopExtenalIds('<br />') . '</span>';
                            break;
                        case 'season':
                            $row['season'] = '<span class="small">' . $product->productSeason->name . " " . $product->productSeason->year . '</span>';
                            break;
                        case 'hasDetails':
                            $row['hasDetails'] = (2 < $product->productSheetActual->count()) ? 'sì' : 'no';
                            $row['details'] = "";
                            foreach ($product->productSheetActual as $k => $v) {
                                if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                                    $row['details'] .= '<span class="small">' . $v->productDetail->productDetailTranslation->getFirst()->name . "</span><br />";
                                }
                            }
                            break;
                    }
                }
            }

            $datatable->setResponseDataSetRow($key, $row);
        }


        return $datatable->responseOut();

    }

}