<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductFastListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/05/2020
 * @since 1.0
 */
class CProductSoldListAjaxController extends AAjaxController
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
        if (isset($_REQUEST['dateStart'])) {
            $dateStart = (new \DateTime($_REQUEST['dateStart']))->format('Y-m-d H:i:s');
        }else{
            $dateStart = (new \DateTime())->modify("midnight")->format('Y-m-d H:i:s');
        }
        if (isset($_REQUEST['dateEnd'])) {
            $dateEnd = (new \DateTime($_REQUEST['dateEnd']))->format('Y-m-d H:i:s');
        }else{
            $dateEnd= (new \DateTime())->modify("tomorrow midnight")->format('Y-m-d H:i:s');
        }

        if (isset($_REQUEST['stored'])) {
            $stored = $_REQUEST['stored'];
        } else {
            $stored = '';
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
        if (isset($_REQUEST['productBrandid'])) {
            $productBrandId = $_REQUEST['productBrandid'];
        } else {
            $productBrandId = '';
        }
        if (isset($_REQUEST['productShopid'])) {
            $shopid = $_REQUEST['productShopid'];
        } else {
            $shopid = '';
        }

        if ($season == 0) {
            $sqlFilterSeason = '';
        } else {
            $sqlFilterSeason = ' and p.productSeasonId=' . $productSeasonId;
        }
        if ($productZeroQuantity == 1) {
            $sqlFilterQuantity = 'and p.qty > 0';
        } else {
            $sqlFilterQuantity = '';
        }
        if ($productStatus == '1') {
            $sqlFilterStatus = 'and p.productStatusId=6';

        } else {
            $sqlFilterStatus = '';
        }
        if ($productBrandId == 0) {
            $sqlFilterBrand = '';
        } else {
            $sqlFilterBrand = 'and p.productBrandId=' . $productBrandId;
        }
        if ($shopid == 0) {
            $sqlFilterShop = '';
        } else {
            $sqlFilterShop = 'and s.id=' . $shopid;
        }
        if ($stored == 0) {
            $sqlFilterStored = '';
        } else {
            $sqlFilterStored = 'and p.stored=' . $stored;
        }



        $sql = "SELECT
                  concat(p.id, '-', pv.id)                                                                      AS code,
                  p.id                                                                                              AS id,
                  p.productVariantId                                                                                AS productVariantId,
                  concat(pse.name, ' ', pse.year)                                                               AS season,
                  pse.isActive                                                                                      AS isActive,
                  concat(p.itemno, ' # ', pv.name)                                                              AS cpf,
                  pv.description                                                                                    AS colorNameManufacturer,
                  concat(s.id, '-', s.name)                                                                     AS shop,
                  concat(ifnull(p.externalId, ''), '-', ifnull(dp.extId, ''), '-', ifnull(ds.extSkuId, '')) AS externalId,
                  pb.name                                                                                           AS brand,          
                  psd.startQuantity as starQuantity,
                  psd.endQuantity as endQuantity,    
                  psd.soldQuantity as qty,
                  psd.netTotal as netTotal,
                  concat(psg.locale, ' - ',
                         psmg.name)                                                                                 AS productSizeGroup,
                  p.creationDate                                                                                    AS creationDate,                                 
                  s.id                                                                                              AS shopId,
                  s.name                                                                                            AS row_shop,
                 
                
                  if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
               
                  pc.id                                                                                             AS categoryId,
                  pcg.name                                                                                          AS colorGroup,
                  p.isOnSale                                                                                        AS isOnSale
               
                
                
              
               
                FROM Product p
                    join ProductSoldDay psd on psd.productId=p.id and psd.productVariantId=p.productVariantId
                  JOIN ProductSeason pse ON p.productSeasonId = pse.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductStatus ps ON ps.id = p.productStatusId
                  JOIN ShopHasProduct sp
                    ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
                  JOIN Shop s ON s.id = sp.shopId
                  LEFT JOIN (ProductSizeGroup psg
                              JOIN ProductSizeMacroGroup psmg ON psg.productSizeMacroGroupId = psmg.id)
                            ON p.productSizeGroupId = psg.id
                 
                  LEFT JOIN (ProductHasProductCategory ppc
                              JOIN ProductCategory pc ON ppc.productCategoryId = pc.id
                    ) ON (p.id, p.productVariantId) = (ppc.productId,ppc.productVariantId)
                  LEFT JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
                   JOIN (DirtyProduct dp
                              JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
                    ON (sp.productId,sp.productVariantId,sp.shopId) = (dp.productId,dp.productVariantId,dp.shopId)
                    where   psd.soldQuantity > 0  and psd.dateStart >= '".$dateStart."' and  psd.dateEnd<='".$dateEnd."' ".$sqlFilterSeason . " ". $sqlFilterQuantity . " " . $sqlFilterStatus . " " . $sqlFilterBrand . " " . $sqlFilterShop . ' ' . $sqlFilterStored . " 
                    GROUP by psd.productId, psd.productVariantId";
\Monkey::app()->applicationLog('ProductSoldListAjaxController','log','query',$sql,'-'.$sql);


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
        $productSoldDayRepo=\Monkey::app()->repoFactory->create('ProductSoldDay');

        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');
        $datatable->doAllTheThings();

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CProduct */
            $val = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $val->printId();
            $row['video'] = $val->dummyVideo;
            $row["DT_RowClass"] = $val->productStatus->isVisible == 1 ? 'verde' : (
            $val->productStatus->isReady == 1 ? 'arancione' : ""
            );

            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
            $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-",$val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span>' : '';
            $row['row_dummyUrl'] = $val->getDummyPictureUrl();
            $row['productCard'] = (!$val->getProductCardUrl() ? '-' : '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getProductCardUrl() . '"/></a>');
            $row['row_pCardUrl'] = (!$val->getProductCardUrl() ? '-' : $val->getProductCardUrl());
            $row['details'] = "";
            foreach ($val->productSheetActual as $k => $v) {
                if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                    $row['details'] .= '<span class="small">' . $v->productDetail->productDetailTranslation->getFirst()->name . "</span><br />";
                }
            }
            $row['dummyPicture'] = $val->getDummyPictureUrl();
            $row['hasPhotos'] = ($val->productPhoto->count()) ? 'sì' : 'no';
            $row['dummyVideo'] = ($val->dummyVideo != null) ? 'sì' : 'no';
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

            $onlyCatalogue = '';
            if ($val->onlyCatalogue == 1) {
                $onlyCatalogue = 'solo catalogo';
            } else {
                $onlyCatalogue = 'in vendita';
            }
            $row['status'] = $val->productStatus->name . ' ' . $onlyCatalogue;

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
            $countShooting = 0;
            foreach ($val->shooting as $singleShooting) {
                $sids .= '<br />' . $singleShooting->id;
                $ddtNumbers .= '<br />' . $docRepo->findShootingFriendDdt($singleShooting);
                $countShooting++;
            }
            if ($countShooting > 0) {
                $row['shooting'] = 'sì';

                $row["shooting"] = $sids;
            } else {
                $row['shooting'] = 'no';
            }
            $row["doc_number"] = $ddtNumbers;


            $datatable->setResponseDataSetRow($key,$row);
        }
        return $datatable->responseOut();
    }
}