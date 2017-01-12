<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

/**
 * Class CMarketplaceProductStatisticListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CMarketplaceProductStatisticListAjaxController extends AAjaxController
{
    public function get()
    {
        $marketplaceAccountId = $this->app->router->request()->getRequestData('MarketplaceAccount');
        $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);
        $campaign = \Monkey::app()->repoFactory->create('Campaign')->readCampaignCode($marketplaceAccount->getCampaignCode());

        $query = "SELECT
                      concat(`p`.`id`, '-', `p`.`productVariantId`) AS `codice`,
                      `p`.`id`                                      AS `productId`,
                      `p`.`productVariantId`                        AS `productVariantId`,
                      `p`.`itemno`                                  AS `itemno`,
                      concat(`pss`.`name`, `pss`.`year`)            AS `season`,
                      `pb`.`name`                                   AS `brand`,
                      `p`.`creationDate`                            AS `creationDate`,
                      concat(`m`.`name`, ' - ', `ma`.`name`)        AS `marketplaceAccountName`,
                      `s`.`name`                                    AS `shop`,
                      `s`.`id`                                      AS `shopId`,
                      `mahp`.`marketplaceProductId`                 AS `marketplaceProductId`,
                      `mahp`.`marketplaceId`                        AS `marketplaceId`,
                      `mahp`.`marketplaceAccountId`                 AS `marketplaceAccountId`,
                      `mahp`.`fee`                                  AS `fee`,
                      p.qty                                         AS stock,
                      if(mahp.isToWork = 1,'sì','no') as isToWork,
                      if(mahp.hasError = 1,'sì','no') as hasError,
                      if(mahp.isDeleted = 1,'sì','no') as isDeleted,
                      cv.timestamp                                    AS visitTimestamp,
                      cv.id                                           AS visitId,
                      count(DISTINCT cv.id)                           AS visits,
                      count(distinct cvho.orderId)                    AS conversions,
                      group_concat(distinct ol.orderId SEPARATOR ',') AS ordersIds,
                      phpc.productCategoryId                          AS categories,
                      ifnull(c.code, '')                              AS campaignCode,
                      if(p.isOnSale = 0, min(shp.price),min(shp.salePrice)) as activePrice
                    FROM `Product` `p`
                      JOIN `ProductStatus` `ps` ON ((`p`.`productStatusId` = `ps`.`id`))
                      JOIN `ShopHasProduct` `shp`
                        ON (((`p`.`id` = `shp`.`productId`) AND (`p`.`productVariantId` = `shp`.`productVariantId`)))
                      JOIN `Shop` `s` ON ((`s`.`id` = `shp`.`shopId`))
                      JOIN `ProductSeason` `pss` ON ((`pss`.`id` = `p`.`productSeasonId`))
                      JOIN `ProductBrand` `pb` ON ((`p`.`productBrandId` = `pb`.`id`))
                      JOIN ProductHasProductCategory phpc ON (p.id = phpc.productId AND p.productVariantId = phpc.productVariantId)
                      JOIN `MarketplaceAccountHasProduct` `mahp`
                        ON (((`mahp`.`productId` = `p`.`id`) AND (`mahp`.`productVariantId` = `p`.`productVariantId`)))
                      JOIN `MarketplaceAccount` `ma`
                        ON (((`ma`.`marketplaceId` = `mahp`.`marketplaceId`) AND (`ma`.`id` = `mahp`.`marketplaceAccountId`)))
                      JOIN `Marketplace` `m` ON ((`m`.`id` = `ma`.`marketplaceId`))
                      JOIN Campaign c on c.id = ? 
                      LEFT JOIN (CampaignVisit cv  
                          JOIN CampaignVisitHasProduct cvhp ON cvhp.campaignId = cv.campaignId AND cvhp.campaignVisitId = cv.id )
                      ON c.id = cv.campaignId AND cvhp.productId = p.id AND cvhp.productVariantId = p.productVariantId
                      LEFT JOIN (
                            CampaignVisitHasOrder cvho JOIN
                            `Order` o ON cvho.orderId = o.id JOIN
                            OrderLine ol ON cvho.orderId = ol.orderId )
                                ON cvho.campaignVisitId = cv.id and 
                                   cvho.campaignId = cv.campaignId and 
                                   p.id = ol.productId AND
                                   p.productVariantId = ol.productVariantId 
                    WHERE
                      ma.id = ? AND 
                      ma.marketplaceId = ? AND
                      (ifnull(timestamp,1) BETWEEN ifnull(?,1) and ifnull(?,1)
                       OR o.orderDate between ? and ?) 
                    GROUP BY productId, productVariantId,productCategoryId";

        //IL PROBLEMA é IL DIOCANE DI TIMESTAMP CHE RIMANE NULL DI MERDA DI DIO
        $timeFrom = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('startDate'));
        $timeTo = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('endDate'));
        $timeFrom = $timeFrom ? $timeFrom->format('Y-m-d') : null;
        $timeTo = $timeTo ? $timeTo->format('Y-m-d') : null;
        $queryParameters = [$campaign->id,$marketplaceAccount->id, $marketplaceAccount->marketplaceId, $timeFrom, $timeTo,$timeFrom, $timeTo ];

        $datatable = new CDataTables($query, ['productId','productVariantId'], $_GET, true);
        $datatable->addCondition('shopId', $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $datatable->addSearchColumn('marketplaceProductId');

        $prodottiMarks = $this->app->dbAdapter->query($datatable->getQuery(false, true), array_merge($queryParameters, $datatable->getParams()))->fetchAll();
        $count = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery(true), array_merge($queryParameters, $datatable->getParams()));
        $totalCount = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery('full'), array_merge($queryParameters, $datatable->getParams()));

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($prodottiMarks as $values) {

            $row = [];
            $prodottiMark = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->findOneBy([
                'marketplaceId' => $values['marketplaceId'],
                'marketplaceAccountId' => $values['marketplaceAccountId'],
                'productId' => $values['productId'],
                'productVariantId' => $values['productVariantId'],
            ]);
            /** @var CProduct $val */
            $val = $prodottiMark->product;

            $img = $val->getDummyPictureUrl();
            if ($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';
            $row['codice'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->printId() . '</a>';
            $row['marketCode'] = $prodottiMark->printId();
            $row['brand'] = $val->productBrand->name;
            $row['season'] = $val->productSeason->name;

            $sizes = [];
            $qty = [];
            foreach ($val->productSku as $productSku) {
                if ($productSku->stockQty > 0) {
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

            $row['shop'] = $val->getShops('<br>');
            $row['dummy'] = '<img width="50" src="' . $img . '" />' . $imgs . '<br />';
            $row['itemno'] = '<span class="small">';
            $row['itemno'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $row['itemno'] .= '</span>';

            $row['fee'] = $prodottiMark->fee;
            $row['isToWork'] = $prodottiMark->isToWork ? 'sì' : 'no';
            $row['hasError'] = $prodottiMark->hasError ? 'sì' : 'no';
            $row['isDeleted'] = $prodottiMark->isDeleted ? 'sì' : 'no';
            $row['marketplaceAccountName'] = $prodottiMark->marketplaceAccount->marketplace->name;
            $row['creationDate'] = $val->creationDate;
            $row['categories'] = $val->getLocalizedProductCategories("<br>");
            $row['conversions'] = $values['conversions'];
            $row['visits'] = $values['visits'];
            $row['visitsCost'] = $values['visits'] * ($prodottiMark->fee == 0 ? ($marketplaceAccount->config['defaultCpc'] ?? 0) : $prodottiMark->fee);
            $row['conversionValue'] = 0;
            foreach(explode(',',$values['ordersIds']) as $ordersId) {
                if(empty($ordersId)) continue;
                $order = $this->app->repoFactory->create('Order')->findOne([$ordersId]);
                $row['conversionValue'] += $order->netTotal;
            }
            $row['activePrice'] = $values['activePrice'];

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}