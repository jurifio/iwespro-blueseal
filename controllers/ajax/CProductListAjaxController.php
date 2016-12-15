<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
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
class CProductListAjaxController extends AAjaxController
{
    public function get()
    {
        $response = [];

        $sql = "SELECT
                  concat(`p`.`id`, '-', `pv`.`id`)                                                                      AS `code`,
                  `p`.`id`                                                                                              AS `id`,
                  `p`.`productVariantId`                                                                                AS `productVariantId`,
                  concat(`pse`.`name`, ' ', `pse`.`year`)                                                               AS `season`,
                  `pse`.`isActive`                                                                                      AS `isActive`,
                  concat(`p`.`itemno`, ' # ', `pv`.`name`)                                                              AS `cpf`,
                  `pv`.`description`                                                                                    AS `colorNameManufacturer`,
                  `s`.`name`                                                                                            AS `shop`,
                  concat(ifnull(`p`.`externalId`, ''), '-', ifnull(`dp`.`extId`, ''), '-', ifnull(`ds`.`extSkuId`, '')) AS `externalId`,
                  `pb`.`name`                                                                                           AS `brand`,
                  `ps`.`name`                                                                                           AS `status`,
                  concat(`psg`.`locale`, ' - ',
                         `psg`.`macroName`)                                                                             AS `productSizeGroup`,
                  `p`.`creationDate`                                                                                    AS `creationDate`,
                  `p`.`sortingPriorityId`                                                                               AS `productPriority`,
                  `s`.`id`                                                                                              AS `shopId`,
                  if(((SELECT count(0)
                       FROM `ProductSheetActual`
                       WHERE ((`ProductSheetActual`.`productId` = `p`.`id`) AND
                              (`ProductSheetActual`.`productVariantId` = `p`.`productVariantId`))) > 2), 'sì', 'no')    AS `hasDetails`,
                  if((isnull(`p`.`dummyPicture`) OR (`p`.`dummyPicture` = 'bs-dummy-16-9.png')), 'no', 'sì')            AS `dummy`,
                  if((`p`.`id`, `p`.`productVariantId`) IN (SELECT
                                                              `ProductHasProductPhoto`.`productId`,
                                                              `ProductHasProductPhoto`.`productVariantId`
                                                            FROM `ProductHasProductPhoto`), 'sì', 'no')                 AS `hasPhotos`,
                  `pc`.`id`                                                                                             AS `categoryId`,
                  `pcg`.`name`                                                                                          AS `colorGroup`,
                  `psk`.`isOnSale`                                                                                      AS `isOnSale`,
                  ((((if((`psk`.`isOnSale` = 1), `psk`.`price`, `psk`.`salePrice`) / 1.22) - (`psk`.`value` + ((`psk`.`value` * if(
                      (`pse`.`isActive` = 0), `s`.`pastSeasonMultiplier`,
                      if((`psk`.`isOnSale` = 1), `s`.`saleMultiplier`, `s`.`currentSeasonMultiplier`))) / 100))) /
                    (if((`psk`.`isOnSale` = 1), `psk`.`price`, `psk`.`salePrice`) / 1.22)) * 100)                       AS `mup`,
                  `p`.`qty`                                                                                             AS `hasQty`,
                  `t`.`name`                                                                                            AS `tags`,
                  (SELECT group_concat(concat(m.name, ' - ', ma.name))
                   FROM Marketplace m, MarketplaceAccount ma, MarketplaceAccountHasProduct mahp
                   WHERE m.id = ma.marketplaceId AND
                         ma.id = mahp.marketplaceAccountId AND
                         ma.marketplaceId = mahp.marketplaceId AND
                         mahp.productId = p.id AND
                         mahp.productVariantId = p.productVariantId)                                                    AS marketplaces
                FROM (((((((((((((`Product` `p`
                  JOIN `ProductSeason` `pse` ON ((`p`.`productSeasonId` = `pse`.`id`)))
                  JOIN `ProductVariant` `pv` ON ((`p`.`productVariantId` = `pv`.`id`)))
                  JOIN `ProductBrand` `pb` ON ((`p`.`productBrandId` = `pb`.`id`)))
                  JOIN `ProductStatus` `ps` ON ((`ps`.`id` = `p`.`productStatusId`)))
                  JOIN `ShopHasProduct` `sp`
                    ON (((`p`.`id` = `sp`.`productId`) AND (`p`.`productVariantId` = `sp`.`productVariantId`))))
                  JOIN `Shop` `s` ON ((`s`.`id` = `sp`.`shopId`)))
                  LEFT JOIN `ProductSizeGroup` `psg` ON ((`p`.`productSizeGroupId` = `psg`.`id`)))
                  LEFT JOIN `ProductSku` `psk`
                    ON (((`p`.`id` = `psk`.`productId`) AND (`p`.`productVariantId` = `psk`.`productVariantId`))))
                  LEFT JOIN (`ProductHasProductCategory` `ppc`
                    JOIN `ProductCategory` `pc` ON ((`ppc`.`productCategoryId` = `pc`.`id`)))
                    ON (((`p`.`id` = `ppc`.`productId`) AND (`p`.`productVariantId` = `ppc`.`productVariantId`))))
                  LEFT JOIN (`ProductHasTag` `pht`
                    JOIN `TagTranslation` `t` ON ((`pht`.`tagId` = `t`.`tagId`)))
                    ON (((`pht`.`productId` = `p`.`id`) AND (`pht`.`productVariantId` = `p`.`productVariantId`))))
                  LEFT JOIN (`ProductHasProductColorGroup` `phcg`
                    JOIN `ProductColorGroup` `pcg` ON (((`phcg`.`productColorGroupId` = `pcg`.`id`) AND (`pcg`.`langId` = 1))))
                    ON (((`phcg`.`productId` = `p`.`id`) AND (`phcg`.`productVariantId` = `p`.`productVariantId`))))
                  LEFT JOIN (`DirtyProduct` `dp`
                    JOIN `DirtySku` `ds` ON ((`dp`.`id` = `ds`.`dirtyProductId`)))
                    ON (((`sp`.`productId` = `dp`.`productId`) AND (`sp`.`productVariantId` = `dp`.`productVariantId`) AND
                         (`sp`.`shopId` = `dp`.`shopId`))))
                  LEFT JOIN `ProductNameTranslation` `pnt`
                    ON (((`p`.`id` = `pnt`.`productId`) AND (`p`.`productVariantId` = `pnt`.`productVariantId`) AND
                  (`pnt`.`langId` = 1))))";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET,true);
        $shopIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $datatable->addCondition('shopId', $shopIds);


        $time = microtime(true);
        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $response['queryTime'] = microtime(true) - $time;

        $time = microtime(true);
        $count = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $response['countTime'] = microtime(true) - $time;

        $time = microtime(true);
        $totalCount = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $response['fullCountTime'] = microtime(true) - $time;

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99', '');

        $statuses = [];
        foreach ($productStatuses as $status) {
            $statuses[$status->code] = $status->name;
        }

        $modifica = $this->app->baseUrl(false) . "/blueseal/friend/prodotti/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');


        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $time = microtime(true);
        /** @var $val CProduct */
        foreach ($prodotti as $val) {
            $row = [];

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';

            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;
            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>';
            $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale . '-' . explode("-", $val->productSizeGroup->macroName)[0] . '</span>' : '';

            $row['details'] = "";
            foreach ($val->productSheetActual as $k => $v) {
                if ($trans = $v->productDetail->productDetailTranslation->getFirst()) {
                    $row['details'] .= '<span class="small">' . $trans->name . "</span><br />";
                }
            }

            $row['hasPhotos'] = ($val->productPhoto->count()) ? 'sì' : 'no';
            $row['hasDetails'] = (2 < $val->productSheetActual->count()) ? 'sì' : 'no';
            $row['season'] = '<span class="small">' . $val->productSeason->name . " " . $val->productSeason->year . '</span>';

            $ext = [];
            if (isset($val->externalId) && !empty($val->externalId)) {
                $ext[] = $val->externalId;
            }

            $sizes = [];
            $qty = [];
            foreach ($val->productSku as $productSku) {
                if (in_array($productSku->shopId, $shopIds) && $productSku->stockQty > 0) {
                    $sizes[$productSku->productSizeId] = $productSku->productSize->name;
                    $qty[$productSku->shopId][$productSku->productSizeId] = $productSku->stockQty;
                }
            }
            if (count($sizes) > 0) {
                $table = '<table class="nested-table">';
                $table .= '<thead><tr>';
                if (count($qty) > 1) {
                    $table .= '<th>Shop</th>';
                }
                foreach ($sizes as $sizeId => $name) {
                    $table .= '<th>' . $name . '</th>';
                }
                $table .= '</tr></thead>';
                $table .= '<tbody>';
                foreach ($qty as $shopId => $size) {
                    $table .= '<tr>';
                    if (count($qty) > 1) {
                        $shop = $this->app->repoFactory->create('Shop')->findOne([$shopId]);
                        $table .= '<td>' . $shop->name . '</td>';
                    }
                    foreach ($sizes as $sizeId => $name) {
                        $table .= '<td>' . (isset($size[$sizeId]) ? $size[$sizeId] : 0) . '</td>';
                    }
                    $table .= '</tr>';
                }
                $table .= '</tbody></table>';
            } else {
                $table = 'Quantità non inserite';
            }
            $row['stock'] = $table;

            $shops = [];
            foreach ($val->shopHasProduct as $shp) {
                $shops[] = $shp->shop->name;
                if (!empty($shp->extId)) {
                    $ext[] = $shp->extId;
                } elseif (!is_null($shp->dirtyProduct)) {
                    if (!empty($shp->dirtyProduct->extId)) {
                        $ext[] = $shp->dirtyProduct->extId;
                    }

                    foreach ($shp->dirtyProduct->dirtySku as $sku) {
                        if (!empty($sku->extSkuId)) {
                            $ext[] = $sku->extSkuId;
                        }
                    }
                }
            }

            foreach ($ext as $kext => $vext) {
                $strExt = '<p><span class="small">';
                $arrVext = str_split($vext, 13);
                $strExt .= implode('<br />', $arrVext);
                $strExt .= '</span></p>';
                $ext[$kext] = $strExt;
            }
            $row['externalId'] = implode('', array_unique($ext));

            $row['cpf'] = $val->printCpf();

            $colorGroup = $val->productColorGroup->getFirst();
            $row['colorGroup'] = '<span class="small">' . (($colorGroup) ? $colorGroup->name : "[Non assegnato]") . '</span>';
            $row['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $row['categoryId'] = '<span class="small">' . $val->getLocalizedProductCategories(" ", "<br>") . '</span>';
            $row['description'] = '<span class="small">' . ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "") . '</span>';

            $row['productName'] = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
            $row['tags'] = '<span class="small">' . $val->getLocalizedTags('<br>', false) . '</span>';
            $row['status'] = $val->productStatus->name;
            $row['productPriority'] = $val->sortingPriorityId;

            $qty = 0;
            $isOnSale = [];
            $shopz = [];
            $mup = [];
            foreach ($val->productSku as $sku) {
                $qty += $sku->stockQty;
                $isOnSale = $sku->isOnSale;
                $iShop = $sku->shop->name;
                if (!in_array($iShop, $shopz)) {
                    $shopz[] = $iShop;

                    $price = ($isOnSale) ? $sku->salePrice : $sku->price;

                    if ((float)$price) {
                        $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                        $value = $sku->value;
                        $friendRevenue = $value + $value * $multiplier / 100;
                        $priceNoVat = $price / 1.22;
                        $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100, 2, ",", ".");
                    } else {
                        $mup[] = '-';
                    }
                }
            }
            $row['hasQty'] = $qty;

            $row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>');

            $row['shop'] = '<span class="small">';
            $row['shop'] .= implode('<br />', $shops);
            $row['shop'] .= '</span>';

            $row['mup'] = '<span class="small">';
            $row['mup'] .= implode('<br />', $mup);
            $row['mup'] .= '</span>';

            $row['colorNameManufacturer'] = $val->productVariant->description;

            $row['isOnSale'] = $isOnSale;
            $row['creationDate'] = (new \DateTime($val->creationDate))->format('d-m-Y H:i');

            $response ['data'][] = $row;
        }
        $response['resTime'] = microtime(true) - $time;
        return json_encode($response);
    }
}