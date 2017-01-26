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
                      sum(visitsCost)                                 AS visitsCost,
                      ifnull(conversions,0)                           AS conversions,
                      ifnull(conversionsValue,0)                      as conversionsValue,
                      ifnull(pConversions,0)                           AS pConversions,
                      ifnull(pConversionsValue,0)                      as pConversionsValue,
                      ordersIds                                       AS ordersIds,
                      phpc.productCategoryId                          AS categories,
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
                      LEFT JOIN (SELECT
                                      c.id as campaignId, 
                                      c.marketplaceId,
                                      c.marketplaceAccountId,
                                      cvhp.productId,
                                      cvhp.productVariantId,
                                      ifnull(sum(ol.netPrice),0)                      AS conversionsValue,
                                      sum(CASE WHEN
                                        ol.productId = cvhp.productId AND
                                        ol.productVariantId = cvhp.productVariantId
                                        THEN ol.netPrice
                                          ELSE 0 END)                                 AS pConversionsValue,
                                      group_concat(DISTINCT ol.orderId SEPARATOR ',') AS ordersIds,
                                      count(DISTINCT cv.id)                           AS visits,
                                      count(DISTINCT o.id)                            AS conversions, #conversioni totali di questa visita
                                      sum(cv.cost) as visitsCost,
                                      count(CASE WHEN
                                        ol.productId = cvhp.productId AND
                                        ol.productVariantId = cvhp.productVariantId
                                        THEN o.id
                                            ELSE NULL END)                            AS pConversions #conversioni totali di questa visita per questo prodotto
                                    FROM 
                                    Campaign c 
                                      JOIN CampaignVisit cv on cv.campaignId = c.id
                                      LEFT JOIN CampaignVisitHasProduct cvhp ON cvhp.campaignId = cv.campaignId AND cvhp.campaignVisitId = cv.id
                                      LEFT JOIN (CampaignVisitHasOrder cvho
                                        JOIN `Order` o ON cvho.orderId = o.id
                                        JOIN OrderLine ol ON o.id = ol.orderId
                                        ) ON cvho.campaignVisitId = cv.id AND
                                             cvho.campaignId = cv.campaignId
                                    WHERE (timestamp BETWEEN ifnull(?, timestamp) AND ifnull(?, timestamp) OR
                                           o.orderDate BETWEEN ifnull(?, o.orderDate) AND ifnull(?, o.orderDate))
                                    GROUP BY cvhp.productId, cvhp.productVariantId, cvhp.campaignId
                                    ) sql2 on sql2.productId = mahp.productId and sql2.productVariantId = mahp.productVariantId and sql2.marketplaceId = mahp.marketplaceId and sql2.marketplaceAccountId = mahp.marketplaceAccountId
                    WHERE
                      ma.id = ? AND 
                      ma.marketplaceId = ?
                    GROUP BY productId, productVariantId,productCategoryId order by visits desc";

        //IL PROBLEMA é IL DIOCANE DI TIMESTAMP CHE RIMANE NULL DI MERDA DI DIO
        $timeFrom = new \DateTime($this->app->router->request()->getRequestData('startDate').' 00:00:00');
        $timeTo = new \DateTime($this->app->router->request()->getRequestData('endDate').' 00:00:00');

        $timeFrom = $timeFrom ? $this->time($timeFrom->getTimestamp()) : null;
        $timeTo = $timeTo ? $this->time($timeTo->getTimestamp()) : null;
        $queryParameters = [$timeFrom, $timeTo,$timeFrom, $timeTo,$marketplaceAccount->id, $marketplaceAccount->marketplaceId ];

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

            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$val->printId().'"></table>';;

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
            $row['pConversions'] = $values['pConversions'];
            $row['visits'] = $values['visits'];
            $row['visitsCost'] = $values['visitsCost'];
            $row['conversionValue'] = $values['conversionsValue'];
            $row['pConversionsValue'] = $values['pConversionsValue'];
            $row['activePrice'] = $values['activePrice'];
            $row['ordersIds'] = $values['ordersIds'];

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}