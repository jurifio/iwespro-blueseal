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
                  concat(`s`.`id`, '-', `s`.`name`)                                                                   AS `shop`,
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
                  `p`.`isOnSale`                                                                                      AS `isOnSale`,
                  (((if((`p`.`isOnSale` = 0), `psk`.`price`, `psk`.`salePrice`) / 1.22) - (`psk`.`value` + ((`psk`.`value` * if(
                      (`pse`.`isActive` = 0), `s`.`pastSeasonMultiplier`,
                      if((`p`.`isOnSale` = 1), `s`.`saleMultiplier`, `s`.`currentSeasonMultiplier`))) / 100))) /
                   (if((`p`.`isOnSale` = 0), `psk`.`price`, `psk`.`salePrice`) / 1.22)) * 100                       AS `mup`,
                  `p`.`qty`                                                                                             AS `hasQty`,
                  (SELECT group_concat(DISTINCT t.name) FROM `ProductHasTag` `pht` JOIN `TagTranslation` `t` ON `pht`.`tagId` = `t`.`tagId`
                   WHERE langId = 1 AND pht.productId = `p`.id AND `pht`.`productVariantId` = `p`.`productVariantId` GROUP BY p.productVariantId) as `tags`,
                  (select min(if(ProductSku.stockQty > 0, if(p.isOnSale = 0, ProductSku.price, ProductSku.salePrice), null)) from ProductSku where ProductSku.productId = p.id and ProductSku.productVariantId = p.productVariantId) as activePrice,
                  (SELECT ifnull(group_concat(concat(m.name, ' - ', ma.name)),'')
                   FROM Marketplace m 
                      join MarketplaceAccount ma on (m.id = ma.marketplaceId) 
                      JOIN MarketplaceAccountHasProduct mahp on 
                          ma.id = mahp.marketplaceAccountId AND
                          ma.marketplaceId = mahp.marketplaceId
                   WHERE mahp.productId = p.id AND
                         mahp.productVariantId = p.productVariantId and mahp.isDeleted != 1)                            AS marketplaces
                FROM (((((((((`Product` `p`
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
                  LEFT JOIN `ProductColorGroup` `pcg` ON (`p`.`productColorGroupId` = `pcg`.`id`)
                  LEFT JOIN (`DirtyProduct` `dp`
                    JOIN `DirtySku` `ds` ON ((`dp`.`id` = `ds`.`dirtyProductId`)))
                    ON (((`sp`.`productId` = `dp`.`productId`) AND (`sp`.`productVariantId` = `dp`.`productVariantId`) AND
                         (`sp`.`shopId` = `dp`.`shopId`))) ";

        $shootingCritical = \Monkey::app()->router->request()->getRequestData('shootingCritical');
        if ($shootingCritical)  $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11)";
        $productDetailCritical = \Monkey::app()->router->request()->getRequestData('detailsCritical');
        if ($productDetailCritical) $sql .= " AND `p`.`dummyPicture` not like '%dummy%' AND `p`.`productStatusId` in (4,5,11) HAVING `hasDetails` = 'no'";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET,true);
        $shopIds = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $datatable->addCondition('shopId', $shopIds);

        $dataTableQuery = $datatable->getQuery();
        $dataTableParams = $datatable->getParams();
        $time = microtime(true);
        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($dataTableQuery, $dataTableParams);
        $datatable->responseSet['selectTime'] = microtime(true) - $time;

        $time = microtime(true);
        $response ['recordsFiltered'] = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $datatable->responseSet['countTime'] = microtime(true) - $time;

        $time = microtime(true);
        $datatable->responseSet['recordsTotal'] = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $datatable->responseSet['fullCountTime'] = microtime(true) - $time;

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99', '');

        $statuses = [];
        foreach ($productStatuses as $status) {
            $statuses[$status->code] = $status->name;
        }

        $modifica = $this->app->baseUrl(false) . "/blueseal/friend/prodotti/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

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

            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$val->printId().'"></table>';
            $row['externalId'] = '<span class="small">'.$val->getShopExtenalIds('<br />').'</span>';

            $row['cpf'] = $val->printCpf();

            $row['colorGroup'] = '<span class="small">' . (!is_null($val->productColorGroup) ? $val->productColorGroup->productColorGroupTranslation->getFirst()->name : "[Non assegnato]") . '</span>';
            $row['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $row['categoryId'] = '<span class="small">' . $val->getLocalizedProductCategories(" ", "<br>") . '</span>';
            $row['description'] = '<span class="small">' . ($val->productDescriptionTranslation->getFirst() ? $val->productDescriptionTranslation->getFirst()->description : "") . '</span>';

            $row['productName'] = $val->productNameTranslation->getFirst() ? $val->productNameTranslation->getFirst()->name : "";
            $row['tags'] = '<span class="small">' . $val->getLocalizedTags('<br>', false) . '</span>';
            $row['status'] = $val->productStatus->name;
            $row['productPriority'] = $val->sortingPriorityId;

            $qty = 0;
            $shopz = [];
            $mup = [];
            $isOnSale = $val->isOnSale();
            foreach ($val->productSku as $sku) {
                $qty += $sku->stockQty;
                $iShop = $sku->shop->name;
                if (!in_array($iShop, $shopz)) {
                    $shopz[] = $iShop;

                    $price = $isOnSale ? $sku->salePrice : $sku->price;

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
            $row['activePrice'] = $val->getDisplayActivePrice() ? $val->getDisplayActivePrice() : 'Non Assegnato';

            $row['marketplaces'] = $val->getMarketplaceAccountsName(' - ','<br>',true);

            $row['shop'] = '<span class="small">'.$val->getShops('<br />', true).'</span>';

            $row['mup'] = '<span class="small">';
            $row['mup'] .= implode('<br />', $mup);
            $row['mup'] .= '</span>';

            $row['colorNameManufacturer'] = $val->productVariant->description;

            $row['isOnSale'] = $val->isOnSale();
            $row['creationDate'] = (new \DateTime($val->creationDate))->format('d-m-Y H:i');

            $datatable->responseSet ['data'][] = $row;
        }
        $datatable->responseSet['resTime'] = microtime(true) - $time;
        return $datatable->responseOut();
    }
}