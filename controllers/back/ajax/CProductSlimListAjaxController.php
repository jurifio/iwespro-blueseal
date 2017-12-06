<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;

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

        $sql = "SELECT
                  `p`.`id`                                             AS `id`,
                  `p`.`productVariantId`                               AS `productVariantId`,
                  concat(`p`.`id`, '-', `p`.`productVariantId`)        AS `code`,
                  `pb`.`name`                                          AS `brand`,
                  concat(`p`.`itemno`, ' # ', `pv`.`name`)             AS `cpf`,
                  shp.extId                                            AS `externalId`,
                  concat(`ps`.`name`, ' ', `ps`.`year`)                AS `season`,
                  `s`.`id`                                             AS `shopId`,
                  `s`.`title`                                          AS `shop`,
                  if((`p`.`qty` > 0), 'disponibile', 'mancante')       AS `stock`,
                  `p`.`creationDate`                                   AS `creationDate`,
                  `pss`.`name`                                         AS `status`,
                  shp.price                                            AS price,
                  shp.salePrice                                        AS salePrice,
                  shp.value                                            AS value
                FROM `Product` `p`
                  JOIN `ProductVariant` `pv` ON `p`.`productVariantId` = `pv`.`id`
                  JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
                  JOIN `ProductStatus` `pss` ON `pss`.`id` = `p`.`productStatusId`
                  JOIN `ShopHasProduct` `shp` ON (`p`.`id`, `p`.`productVariantId`) = (`shp`.`productId`, `shp`.`productVariantId`)
                  JOIN `Shop` `s` ON `s`.`id` = `shp`.`shopId`
                  JOIN `ProductSeason` `ps` ON `p`.`productSeasonId` = `ps`.`id`
                WHERE `pss`.`id` NOT IN (7, 8, 13)
                GROUP BY p.id, p.productVariantId
                ORDER BY `p`.`creationDate` DESC";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET, true);
        $datatable->addCondition('shopId', $shopsIds);
        if (!$allShops) $datatable->addLikeCondition('status', 'Fuso', true);

        $datatable->doAllTheThings();
        $okManage = $user->hasPermission('/admin/product/edit');
        $modifica = '/blueseal/friend/prodotti/modifica';

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
            $datatable->setResponseDataSetRow($key, $row);
        }
        return $datatable->responseOut();
    }
}