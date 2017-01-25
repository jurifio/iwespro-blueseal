<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\traits\TMySQLTimestamp;
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
    use TMySQLTimestamp;

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
                      if(mahp.isToWork = 1,'sìsi','no')               as isToWork,
                      if(mahp.hasError = 1,'sìsi','no')               as hasError,
                      if(mahp.isDeleted = 1,'sìsi','no')              as isDeleted,
                      ifnull(visits,0)                                AS visits,
                      ifnull(conversions,0)                           AS conversions,
                      round(visits*fee)                               AS visitsCost,
                      ordersIds                                       AS ordersIds,
                      ifnull(conversionsValue,0)                      as conversionsValue,
                      phpc.productCategoryId                          AS categories,
                      ifnull(sql2.code, '')                                AS campaignCode,
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
                      LEFT JOIN (SELECT c.code, 
                                        cvhp.productId, 
                                        cvhp.productVariantId, 
                                        sum(ol.netPrice) as conversionsValue, 
                                        group_concat(distinct ol.orderId SEPARATOR ',') AS ordersIds, 
                                        count(cv.id) as visits, 
                                        count(o.id) as conversions
                                    FROM Campaign c 
                                    JOIN CampaignVisit cv on c.id = cv.campaignId 
                                    JOIN CampaignVisitHasProduct cvhp on cvhp.campaignId = cv.campaignId AND cvhp.campaignVisitId = cv.id 
                                    LEFT JOIN (CampaignVisitHasOrder cvho 
                                                JOIN `Order` o on cvho.orderId = o.id 
                                                JOIN OrderLine ol on o.id = ol.orderId
                                                ) on cvho.campaignVisitId = cv.id and 
                                                     cvho.campaignId = cv.campaignId AND 
                                                     ol.productId = cvhp.productId and 
                                                     ol.productVariantId = cvhp.productVariantId
                                    where c.id = ? AND
                                    (timestamp BETWEEN ifnull(?,timestamp) and ifnull(?,timestamp) OR
                                     if(orderDate is null,0=1,o.orderDate BETWEEN ifnull(?,o.orderDate) and ifnull(?,o.orderDate)))
                                    GROUP BY cvhp.productId, cvhp.productVariantId, cvhp.campaignId) sql2 on sql2.productId = mahp.productId and sql2.productVariantId = mahp.productVariantId
                    WHERE
                      ma.id = ? AND 
                      ma.marketplaceId = ?
                    GROUP BY productId, productVariantId,productCategoryId order by visits desc";
        $sub = "";

        /*
         * LEFT JOIN (CampaignVisit cv
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
        */

        //IL PROBLEMA é IL DIOCANE DI TIMESTAMP CHE RIMANE NULL DI MERDA DI DIO
        $timeFrom = new \DateTime($this->app->router->request()->getRequestData('startDate').' 00:00:00');
        $timeTo = new \DateTime($this->app->router->request()->getRequestData('endDate').' 00:00:00');

        $timeFrom = $timeFrom ? $this->time($timeFrom->getTimestamp()) : null;
        $timeTo = $timeTo ? $this->time($timeTo->getTimestamp()) : null;
        $queryParameters = [$campaign->id,$timeFrom, $timeTo,$timeFrom, $timeTo,$marketplaceAccount->id, $marketplaceAccount->marketplaceId ];

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
        $response['queryParams'] = $queryParameters;
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
            $row['visitsCost'] = $values['visitsCost'];
            $row['conversionValue'] = $values['conversionsValue'];
            $row['activePrice'] = $values['activePrice'];
            $row['ordersIds'] = $values['ordersIds'];

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}