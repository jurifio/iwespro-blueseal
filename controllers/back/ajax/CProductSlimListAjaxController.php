<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\controllers\api\classes\products;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CShooting;
use bamboo\domain\repositories\CDocumentRepo;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductSlimListAjaxController extends AAjaxController
{
    public function get()
    {
        $shopsIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();

        $user = \Monkey::app()->getUser();
        $allShops = $user->hasPermission('allShops');
        $productHasShopDestination = \Monkey::app()->repoFactory->create('ProductHasShopDestination');
        $shopRepo= \Monkey::app()->repoFactory->create('Shop');

        $sql = "SELECT
  `p`.`id`                                             AS `id`,
  `p`.`productVariantId`                               AS `productVariantId`,
  concat(`p`.`id`, '-', `p`.`productVariantId`)        AS `code`,
  phsd.shopIdOrigin                                                                             AS shopIdOrigin,
  phsd.shopIdDestination                                                                        AS shopIdDestination,
  `pb`.`name`                                          AS `brand`,
  concat(`p`.`itemno`, ' # ', `pv`.`name`)             AS `cpf`,
  concat(ifnull(p.externalId, ''), '-', ifnull(dp.extId, ''), '-', ifnull(ds.extSkuId, ''))                                           AS `externalId`,
  concat(`ps`.`name`, ' ', `ps`.`year`)                AS `season`,
  `s`.`id`                                             AS `shopId`,
  `s`.`title`                                          AS `shop`,
  if((`p`.`qty` > 0), 'disponibile', 'mancante')       AS `stock`,
  `p`.`creationDate`                                   AS `creationDate`,
  `pss`.`name`                                         AS `status`,
  shp.price                                            AS price,
  shp.salePrice                                        AS salePrice,
  shp.value                                            AS value,
  concat(phs.shootingId)                               AS shooting,
  concat(doc.number)                                   AS doc_number,
  if((p.id, p.productVariantId) IN (SELECT
                                      ProductHasProductPhoto.productId,
                                      ProductHasProductPhoto.productVariantId
                                    FROM ProductHasProductPhoto), 'sì', 'no')                 AS hasPhotos,
  psp.name as prodSheetPrototypeName,
  if(count(DISTINCT ps1.ean) = count(DISTINCT ps1.productSizeId), 'si', 'no') AS ean
FROM `Product` `p`
  JOIN ProductSku ps1 ON p.id = ps1.productId AND p.productVariantId = ps1.productVariantId
  LEFT JOIN ProductHasShopDestination phsd ON p.id =phsd.productId  AND p.productVariantId=phsd.productVariantId
  JOIN `ProductVariant` `pv` ON `p`.`productVariantId` = `pv`.`id`
  JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
  JOIN `ProductStatus` `pss` ON `pss`.`id` = `p`.`productStatusId`
  JOIN `ShopHasProduct` `shp` ON (`p`.`id`, `p`.`productVariantId`) = (`shp`.`productId`, `shp`.`productVariantId`)
  JOIN `Shop` `s` ON `s`.`id` = `shp`.`shopId`
  JOIN `ProductSeason` `ps` ON `p`.`productSeasonId` = `ps`.`id`
  LEFT JOIN (
      ProductHasShooting phs
      JOIN Shooting shoot ON phs.shootingId = shoot.id
      LEFT JOIN Document doc ON shoot.friendDdt = doc.id)
    ON p.productVariantId = phs.productVariantId AND p.id = phs.productId
  LEFT JOIN ProductSheetPrototype psp ON p.productSheetPrototypeId = psp.id
  LEFT JOIN (DirtyProduct dp
    JOIN DirtySku ds ON dp.id = ds.dirtyProductId)
    ON (shp.productId,shp.productVariantId,shp.shopId) = (dp.productId,dp.productVariantId,dp.shopId)
WHERE `pss`.`id` NOT IN (7, 8)
GROUP BY p.id, p.productVariantId, s.id
ORDER BY `p`.`creationDate` DESC";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET, true);
        $datatable->addCondition('shopId', $shopsIds);
        if (!$allShops) $datatable->addLikeCondition('status', 'Fuso', true);

        $datatable->doAllTheThings();
        $okManage = $user->hasPermission('/admin/product/edit');
        $modifica = '/blueseal/friend/prodotti/modifica';

        /** @var CDocumentRepo $docRepo */
        $docRepo = \Monkey::app()->repoFactory->create('Document');

        $productRepo = \Monkey::app()->repoFactory->create('Product');
        foreach ($datatable->getResponseSetData() as $key => $row) {

            $val = $productRepo->findOneBy($row);
            /** @var CProduct $val */
            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';
            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;

            if ($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";
            $row['image'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>' . $imgs . '<br />';

            $row["row_shop"] = $val->getShops('|', true);
            $row['shop'] = '<span>';
            $row['shop'] .= $val->getShops('<br />');
            $row['shop'] .= '</span>';

            $row['details'] = $val->productSheetActual->count() ? 'Sì' : 'No';

            $row['externalId'] = '<span class="small">';
            $row['externalId'] .= $val->getShopExtenalIds('<br />');
            $row['externalId'] .= '</span>';

            $row['cpf'] = '<span class="small">';
            $row['cpf'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $row['cpf'] .= '</span>';

            $row['brand'] = $val->productBrand->name;

            $row['categories'] = '<span class="small">';
            $row['categories'] .= $val->getLocalizedProductCategories('<br />');
            $row['categories'] .= '</span>';

            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="' . $val->printId() . '"></table>';

            $row['season'] = '<span class="small">';
            $row['season'] .= ($val->productSeason) ? $val->productSeason->name . " " . $val->productSeason->year : '-';
            $row['season'] .= '</span>';
            $shopDestination= "";
            $arr = explode("-", $row['DT_RowId']);
            $productId = $arr[0];
            $productVariantId=$arr[1];
            $productHasShopDestinationFind=$productHasShopDestination->findBy(['productId'=>$productId,'productVariantId'=>$productVariantId]);
            foreach($productHasShopDestinationFind as $j){
                $shopNameFind=$shopRepo->findOneBy(['id'=>$j->shopIdDestination]);
                $shopName=$shopNameFind->title;
                $shopDestination = $shopDestination.$j->shopIdDestination."-".$shopName."<br>";

            }

            if($shopDestination==""){
                $row['shopIdDestination']='non Assegnato';
            }else{
                $row['shopIdDestination']='Prodotto Assegnato';
            }

            if ($allShops) $status = $val->productStatus->name;
            else {
                $friendQty = 0;
                foreach ($val->productSku as $sku) {
                    foreach ($shopsIds as $sid) {
                        if ($sku->shopId == $sid) {
                            $friendQty = $friendQty + $sku->stockQty;
                        }
                    }
                }
                if ($friendQty) $status = $val->productStatus->name;
                else $status = 'Esaurito';
            }

            if (count($shopsIds) == 1 || $val->shopHasProduct->count() == 1) {
                foreach ($val->shopHasProduct as $shopHasProduct) {
                    if (in_array($shopHasProduct->shopId, $shopsIds)) {
                        $row['price'] = $shopHasProduct->price;
                        $row['salePrice'] = $shopHasProduct->salePrice;
                        $row['value'] = $shopHasProduct->value;
                    }
                }
            } else {
                foreach ($val->shopHasProduct as $shopHasProduct) {
                    $row['price'] = [];
                    $row['salePrice'] = [];
                    $row['value'] = [];
                    if (in_array($shopHasProduct->shopId, $shopsIds)) {
                        $row['price'][] = $shopHasProduct->shop->name . ': ' . $shopHasProduct->price;
                        $row['salePrice'][] = $shopHasProduct->shop->name . ': ' . $shopHasProduct->salePrice;
                        $row['value'][] = $shopHasProduct->shop->name . ': ' . $shopHasProduct->value;
                    }
                    $row['price'] = implode('<br />', $row['price']);
                    $row['salePrice'] = implode('<br />', $row['salePrice']);
                    $row['value'] = implode('<br />', $row['value']);
                }
            }


            $row['status'] = $status;
            $row['creationDate'] = $val->creationDate;
            $sids = "";
            $ddtNumbers = "";
            /** @var CShooting $singleShooting */
            foreach ($val->shooting as $singleShooting){
                $sids .= '<br />'.$singleShooting->id;
                $ddtNumbers .= '<br />'.$docRepo->findShootingFriendDdt($singleShooting);
            }
            $row["shooting"] = $sids;
            $row["doc_number"] = $ddtNumbers;
            $row['hasPhotos'] = ($val->productPhoto->count()) ? 'sì' : 'no';
            $row['prodSheetPrototypeName'] = $val->productSheetPrototype->name;
            $row['pspRow_Id'] = $val->productSheetPrototypeId;
            $skus = $val->productSku;
            $ean = '';
            foreach ($skus as $sku){
                $ean .= $sku->ean . '</br>';
            }
            $row["ean"] = $ean;
            /** @var CProductSku $sku */
            $complete = true;
            foreach ($val->productSku as $sku){
                if(is_null($sku->ean)) $complete = false;
            }

            if(!$complete){
                $row["DT_RowClass"] = "red";
            }

            $datatable->setResponseDataSetRow($key, $row);
        }
        return $datatable->responseOut();
    }
}