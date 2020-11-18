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
class CProductFastWithImageListAjaxController extends AAjaxController
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
        if (isset($_REQUEST['stored'])) {
            $stored = $_REQUEST['stored'];
        } else {
            $stored = '';
        }
        if (isset($_REQUEST['productShooting'])) {
            $productShooting = $_REQUEST['productShooting'];
        } else {
            $productShooting = '';
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

        if ($season == 1) {
            $sqlFilterSeason = '';
        } else {
            $sqlFilterSeason = ' and p.productSeasonId=' . $productSeasonId;
        }
        if ($productZeroQuantity == 1) {
            $sqlFilterQuantity = '';
        } else {
            $sqlFilterQuantity = 'and p.qty>0';
        }
        if ($productShooting == 1) {
            $sqlFilterShooting = 'and concat(phs.shootingId) is null     ';
        } else {
            $sqlFilterShooting = '';
        }
        if ($productStatus == 1) {
            $sqlFilterStatus = '';
        } else {
            $sqlFilterStatus = 'and p.productStatusId=6';
        }
        if ($productBrandId == 0) {
            $sqlFilterBrand = '';
        } else {
            $sqlFilterBrand = 'and p.productBrandId='.$productBrandId;
        }
        if ($shopid == 0) {
            $sqlFilterShop = '';
        } else {
            $sqlFilterShop = 'and s.id='.$shopid;
        }
        if ($stored == 0) {
            $sqlFilterStored = '';
        } else {
            $sqlFilterStored = 'and p.stored='.$stored;
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
                  concat(ps.name,if(p.onlyCatalogue=1,' solo catalogo',' in vendita'))                                                                                          AS status,
                  concat(psg.locale, ' - ',
                         psmg.name)                                                                                 AS productSizeGroup,
                  p.creationDate                                                                                    AS creationDate,
                  p.sortingPriorityId                                                                               AS productPriority,          
                if(p.onlyCatalogue=1,'solo catalogo','in vendita')                                                                                        As onlyCatalogue,                               
                  s.id                                                                                              AS shopId,
                  s.name                                                                                            AS row_shop,
                  concat(phs.shootingId)                                                             AS shooting,
                  concat(doc.number)                                                             AS doc_number,
                  (SELECT count(*)
                   FROM ShopHasProduct
                   WHERE (ShopHasProduct.productId, ShopHasProduct.productVariantId) = (p.id, p.productVariantId))      AS shops,
                  if(((SELECT count(0)
                       FROM ProductSheetActual
                       WHERE ((ProductSheetActual.productId = p.id) AND
                              (ProductSheetActual.productVariantId = p.productVariantId))) > 2), 'sì', 'no')    AS hasDetails,
                  if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
                  if(isnull(p.dummyVideo), 'no', 'sì')            AS dummyVideo,
                  if((p.id, p.productVariantId) IN (SELECT
                                                              ProductHasProductPhoto.productId,
                                                              ProductHasProductPhoto.productVariantId
                                                            FROM ProductHasProductPhoto), 'sì', 'no')                 AS hasPhotos,
                  pc.id                                                                                             AS categoryId,
                  pcg.name                                                                                          AS colorGroup,
                  p.isOnSale                                                                                        AS isOnSale,
                  psiz.name                                                                                             AS stock,
                  ifnull(p.processing, '-')                                                                         AS processing,
                  #(((if((p.isOnSale = 0), psk.price, psk.salePrice) / 1.22) - (psk.value + ((psk.value * if(
                   #   (pse.isActive = 0), s.pastSeasonMultiplier,
                   #   if((p.isOnSale = 1), s.saleMultiplier, s.currentSeasonMultiplier))) / 100))) /
                   #(if((p.isOnSale = 0), psk.price, psk.salePrice) / 1.22)) * 100                           AS mup,
                  p.qty                                                                                             AS hasQty,
                  (SELECT group_concat(DISTINCT t.name)
                   FROM ProductHasTag pht
                     JOIN TagTranslation t ON pht.tagId = t.tagId
                   WHERE langId = 1 AND pht.productId = p.id AND pht.productVariantId = p.productVariantId)   AS tags,
                  (SELECT min(if(ProductSku.stockQty > 0, if(p.isOnSale = 0, ProductSku.price, ProductSku.salePrice), NULL))
                   FROM ProductSku
                   WHERE ProductSku.productId = p.id AND ProductSku.productVariantId = p.productVariantId)              AS activePrice,
                   (SELECT ifnull(group_concat(distinct ma.name), '')
                   FROM Marketplace m
                     JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
                     JOIN MarketplaceAccountHasProduct mahp ON (ma.id,ma.marketplaceId) = (mahp.marketplaceAccountId,mahp.marketplaceId)
                   WHERE mahp.productId = p.id AND
                         mahp.productVariantId = p.productVariantId AND mahp.isDeleted != 1)                            AS marketplaces,
                         
                if(isnull(prHp.productId), 'no', 'si') inPrestashop
                FROM Product p
                  JOIN ProductSeason pse ON p.productSeasonId = pse.id
                  JOIN ProductVariant pv ON p.productVariantId = pv.id
                  JOIN ProductBrand pb ON p.productBrandId = pb.id
                  JOIN ProductStatus ps ON ps.id = p.productStatusId
                  LEFT JOIN PrestashopHasProduct prHp ON p.id = prHp.productId AND p.productVariantId = prHp.productVariantId
                  JOIN ShopHasProduct sp
                    ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
                  JOIN Shop s ON s.id = sp.shopId
                  LEFT JOIN (ProductSizeGroup psg
                              JOIN ProductSizeMacroGroup psmg ON psg.productSizeMacroGroupId = psmg.id)
                            ON p.productSizeGroupId = psg.id
                  LEFT JOIN (ProductSku psk
                    JOIN ProductSize psiz ON psk.productSizeId = psiz.id)
                    ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)
                  LEFT JOIN (ProductHasProductCategory ppc
                              JOIN ProductCategory pc ON ppc.productCategoryId = pc.id
                    ) ON (p.id, p.productVariantId) = (ppc.productId,ppc.productVariantId)
                  LEFT JOIN ProductColorGroup pcg ON p.productColorGroupId = pcg.id
                  LEFT JOIN (DirtyProduct dp
                              JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
                    ON (sp.productId,sp.productVariantId,sp.shopId) = (dp.productId,dp.productVariantId,dp.shopId)
                    LEFT JOIN (
                    ProductHasShooting phs 
                      JOIN Shooting shoot ON phs.shootingId = shoot.id
                        LEFT JOIN Document doc ON shoot.friendDdt = doc.id) 
                                ON p.productVariantId = phs.productVariantId AND p.id = phs.productId where 1=1 " . $sqlFilterSeason . ' ' . $sqlFilterQuantity . ' ' . $sqlFilterStatus . ' ' . $sqlFilterBrand. ' ' . $sqlFilterShop. ' ' . $sqlFilterStored. ' ' . $sqlFilterShooting;


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
        $productHasProductPhotoRepo=\Monkey::app()->repoFactory->create('ProductHasProductPhoto');
        $productPhotoRepo=\Monkey::app()->repoFactory->create('ProductPhoto');


        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');
        $datatable->doAllTheThings();

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CProduct */
            $val = $productRepo->findOneBy($row);

            $row["DT_RowId"] = $val->printId();
            $row['video'] = $val->dummyVideo;
            $row['video2'] = $val->dummyVideo2;
            $row['video3'] = $val->dummyVideo3;
            $row['vide4'] = $val->dummyVideo4;
            $row["DT_RowClass"] = $val->productStatus->isVisible == 1 ? 'verde' : (
            $val->productStatus->isReady == 1 ? 'arancione' : ""
            );

            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;
                $tableImage='<table>';

            $sqlQuery="SELECT `i`.`name`   as linkimage,i.order AS ord FROM ProductPhoto i
left JOIN ProductHasProductPhoto p ON i.id=p.productPhotoId WHERE i.size='1124' AND p.productId=".$val->id." AND p.productVariantId=".$val->productVariantId." GROUP BY p.productId,p.productVariantId,i.order";
            $resImage=\Monkey::app()->dbAdapter->query($sqlQuery,[])->fetchAll();

            if(count($resImage)>0){
                $tableImage.='<tr>';
            foreach($resImage as $res) {

                        $tableImage.='<td><a href="#1" class="enlarge-your-img"><img width="50" src="https://cdn.iwes.it/'.$val->productBrand->slug.'/'.$res['linkimage'].'"/></a></td>';
            }
            $tableImage.='</tr>';
                $tableImage.='<tr>';
                foreach($resImage as $resu) {
                    $tableImage.='<td>'.$resu['ord'].'</td>';
                }
                $tableImage.='</tr>';
           }else{
               $tableImage.='<tr><td><a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a></td></tr>';
           }
           $tableImage.='</table>';
            $row['dummy'] = $tableImage;
            $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-",$val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span>' : '';
            $row['row_dummyUrl'] = $val->getDummyPictureUrl();
            $row['productCard'] = (!$val->getProductCardUrl() ? '-' :'<a href="#1" class="enlarge-your-img"><img width="50" src="'. $val->getProductCardUrl().'"/></a>');
            $row['row_pCardUrl'] = (!$val->getProductCardUrl() ? '-' : $val->getProductCardUrl());
            $row['details'] = "";
            foreach ($val->productSheetActual as $k => $v) {
                if (!is_null($v->productDetail) && !$v->productDetail->productDetailTranslation->isEmpty()) {
                    $row['details'] .= '<span class="small">' . $v->productDetail->productDetailTranslation->getFirst()->name . "</span><br />";
                }
            }
            $row['dummyPicture']=$val->getDummyPictureUrl();
            $row['hasPhotos'] = ($val->productPhoto->count()) ? 'sì' : 'no';
            if($val->dummyVideo!=null) {
                $dummyVideo = '<table><tr><td align="center"><video id="'.$val->id.'-'.$val->productVariantId.'-1" width="100" height="80" autoplay>
  <source src="' . $val->dummyVideo . '" type="video/mp4">;
</video></td></tr><tr><td align="center">1</td></tr></table>';
            }else{
                $dummyVideo='no';
            }
            if($val->dummyVideo2!=null) {
                $dummyVideo2 = '<table><tr><td align="center"><video id="'.$val->id.'-'.$val->productVariantId.'-2" width="100" height="80" autoplay>
  <source src="' . $val->dummyVideo2 . '" type="video/mp4">;
</video></td></tr><tr><td align="center">2</td></tr></table>';

            }else{
                $dummyVideo2='no';
            }
            if($val->dummyVideo!=null) {
                $dummyVideo3 = '<table><tr><td align="center"><video id="'.$val->id.'-'.$val->productVariantId.'-3" width="100" height="80" autoplay>
  <source src="' . $val->dummyVideo3 . '" type="video/mp4">;
</video></td></tr><tr><td align="center">3</td></tr></table>';
            }else{
                $dummyVideo3='no';
            }
            if($val->dummyVideo4!=null) {
                $dummyVideo4 = '<table><tr><td align="center"><video id="'.$val->id.'-'.$val->productVariantId.'-4" width="100" height="80" autoplay>
  <source src="' . $val->dummyVideo4 . '" type="video/mp4">;
</video></td></tr><tr><td align="center">4</td></tr></table>';
            }else{
                $dummyVideo4='no';
            }
            $row['dummyVideo'] = $dummyVideo;
            $row['dummyVideo2'] = $dummyVideo2;
            $row['dummyVideo3'] = $dummyVideo3;
            $row['dummyVideo4'] = $dummyVideo4;

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
            $onlyCatalogue='';
            if($val->onlyCatalogue==1){
                $onlyCatalogue='solo catalogo';
            }else{
                $onlyCatalogue='in vendita';
            }
            $row['status'] = $val->productStatus->name. ' '.$onlyCatalogue;
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