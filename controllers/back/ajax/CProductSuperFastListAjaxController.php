<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductShopListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/05/2020
 * @since 1.0
 */
class CProductSuperFastListAjaxController extends AAjaxController
{
    public function get()
    {

        $productSeason = \Monkey::app()->dbAdapter->query('select max(id) as productSeasonId from ProductSeason ',[])->fetchAll();
        foreach ($productSeason as $val) {
            $productSeasonId = $val['productSeasonId'];
        }
        //$season=\Monkey::app()->router->request()->getRequestData('season');
        if (isset($_REQUEST['season'])) {
            $season = $_REQUEST['season'];
        } else {
            $season = '';
        }
        if (isset($_REQUEST['productZeroQuantity'])) {
            $productZeroQuantity = $_REQUEST['productZeroQuantity'];
        } else {
            $productZeroQuantity = '';
        }
        if (isset($_REQUEST['productStatus'])) {
            $productStatus = $_REQUEST['productStatus'];
        } else {
            $productStatus = '';
        }

        if ($season == 1) {
            $sqlFilterSeason = '';
        } else {
            $sqlFilterSeason = ' and p.productSeasonId=' . $productSeasonId;
        }
        if ($productZeroQuantity == 1) {
            $sqlFilterQuantity = '';
        } else {
            $sqlFilterQuantity = 'and p.hasQty>0';
        }
        if ($productStatus == 1) {
            $sqlFilterStatus = '';
        } else {
            $sqlFilterStatus = 'and p.productStatusId=6';
        }


        $sql = "SELECT
                  concat(p.id, '-', pv.id)                                                                      AS code,
                  p.id                                                                                              AS id,
                  p.productVariantId                                                                                AS productVariantId,
                  concat(pse.name, ' ', pse.year)                                                               AS season,
                  pse.isActive                                                                                      AS isActive,
                  concat(p.itemno, ' # ', pv.name)                                                              AS cpf,
                  pv.description                                                                                    AS colorNameManufacturer,
                  pb.name                                                                                           AS brand,
                  ps.name                                                                                           AS status,  
                  p.creationDate                                                                                    AS creationDate,
                  p.lastUpdate as lastUpdate,  
                  if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
                  p.isOnSale                                                                                        AS isOnSale,
                  p.qty                                                                                             AS hasQty
                
                FROM Product p
                  JOIN ProductSeason pse ON p.productSeasonId = pse.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductStatus ps ON ps.id = p.productStatusId        
                  


                 WHERE 1=1  " . $sqlFilterSeason . ' ' . $sqlFilterQuantity . ' ' . $sqlFilterStatus;


        $shootingCritical = \Monkey::app()->router->request()->getRequestData('shootingCritical');
        if ($shootingCritical) $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11)";
        $productDetailCritical = \Monkey::app()->router->request()->getRequestData('detailsCritical');
        if ($productDetailCritical) $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11) HAVING `hasDetails` = 'no'";


        $datatable = new CDataTables($sql,['id','productVariantId'],$_GET,true);
        $shopIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $datatable->addCondition('shopId',$shopIds);

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        foreach ($productStatuses as $status) {
            $statuses[$status->code] = $status->name;
        }

        $modifica = $this->app->baseUrl(false) . "/blueseal/friend/prodotti/modifica";
        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');
        $productRepo = \Monkey::app()->repoFactory->create('Product');

        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');
        $datatable->doAllTheThings();

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CProduct */
            $val = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = $val->productStatus->isVisible == 1 ? 'verde' : (
            $val->productStatus->isReady == 1 ? 'arancione' : ""
            );

            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
            $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-",$val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span>' : '';

            $row['details'] = "";
            foreach ($val->productSheetActual as $k => $v) {
                if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                    $row['details'] .= '<span class="small">' . $v->productDetail->productDetailTranslation->getFirst()->name . "</span><br />";
                }
            }

            $row['hasPhotos'] = ($val->productPhoto->count()) ? 'sì' : 'no';
            $row['hasDetails'] = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
            $row['season'] = '<span class="small">' . $val->productSeason->name . " " . $val->productSeason->year . '</span>';

            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';
            $row['externalId'] = '<span class="small">' . $val->getShopExtenalIds('<br />') . '</span>';

            $row['cpf'] = $val->printCpf();

            $row['colorGroup'] = '<span class="small">' . (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $row['categoryId'] = '<span class="small">' . $val->getLocalizedProductCategories('<br>','/') . '</span>';
            $row['description'] = '<span class="small">' . ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "") . '</span>';

            $row['productName'] = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
            $row['tags'] = '<span class="small">' . $val->getLocalizedTags('<br>',false) . '</span>';
            $row['status'] = $val->productStatus->name;
            $row['productPriority'] = $val->sortingPriorityId;

            $qty = 0;
            $shopz = [];
            $mup = [];
            $isOnSale = $val->isOnSale();
            foreach ($val->productSku as $sku) {
                $qty += $sku->stockQty;
                $iShop = $sku->shop->name;
                if (!in_array($iShop,$shopz)) {
                    $shopz[] = $iShop;

                    $price = $isOnSale ? $sku->salePrice : $sku->price;

                    if ((float)$price) {
                        $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                        $value = $sku->value;
                        $friendRevenue = $value + $value * $multiplier / 100;
                        $priceNoVat = $price / 1.22;
                        $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100,2,",",".");
                    } else {
                        $mup[] = '-';
                    }
                }
            }
            $row['hasQty'] = $qty;
            $row['activePrice'] = $val->getDisplayActivePrice() ? $val->getDisplayActivePrice() : 'Non Assegnato';

            //$row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);
            $row['marketplaces'] = "";
            $row["row_shop"] = $val->getShops('|',true);
            $row['shop'] = '<span class="small">' . $val->getShops('<br />',true) . '</span>';
            $row['shops'] = $val->shopHasProduct->count();


            //$row['mup'] = '<span class="small">';
            //$row['mup'] .= implode('<br />', $mup);
            //$row['mup'] .= '</span>';

            $row['friendPrices'] = [];
            $row['friendValues'] = [];
            $row['friendSalePrices'] = [];
            foreach ($val->shopHasProduct as $shp) {
                $row['friendPrices'][] = $shp->price;
                $row['friendValues'][] = $shp->value;
                $row['friendSalePrices'][] = $shp->salePrice;
            }

            $row['friendPrices'] = implode('<br />',$row['friendPrices']);
            $row['friendValues'] = implode('<br />',$row['friendValues']);
            $row['friendSalePrices'] = implode('<br />',$row['friendSalePrices']);

            $row['colorNameManufacturer'] = $val->productVariant->description;

            $row['isOnSale'] = $val->isOnSale();
            $row['creationDate'] = (new \DateTime($val->creationDate))->format('d-m-Y H:i');
            $row['processing'] = ($val->processing) ? $val->processing : '-';

            $sids = "";
            $ddtNumbers = "";
            /** @var CShooting $singleShooting */
            foreach ($val->shooting as $singleShooting) {
                $sids .= '<br />' . $singleShooting->id;
                $ddtNumbers .= '<br />' . $docRepo->findShootingFriendDdt($singleShooting);
            }
            $row["shooting"] = $sids;
            $row["doc_number"] = $ddtNumbers;
            $row["inPrestashop"] = is_null($val->prestashopHasProduct) ? 'no' : 'si';

            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}