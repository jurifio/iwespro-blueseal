<?php
namespace bamboo\controllers\back\ajax;


/**
 * Class AMarketplaceAccountAjaxController
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
abstract class AMarketplaceAccountAjaxController extends AAjaxController
{
    const SQL_SELECT_CAMPAING_PRODUCT_STATISTIC_MARKETPLACE_ACCOUNT =
        "SELECT
                        c.id                                            AS campaignId,
                        c.marketplaceId,
                        c.marketplaceAccountId,
                        cvhp.productId,
                        cvhp.productVariantId,
                        ifnull(sum(ol.netPrice), 0)                     AS conversionsValue,
                        sum(CASE WHEN
                          ol.productId = cvhp.productId AND
                          ol.productVariantId = cvhp.productVariantId
                          THEN ol.netPrice
                            ELSE 0 END)                                 AS pConversionsValue,
                        group_concat(DISTINCT ol.orderId SEPARATOR ',') AS ordersIds,
                        count(DISTINCT cv.id)                           AS visits,
                        count(DISTINCT o.id)                            AS conversions,
                        #conversioni totali di questa visita
                        round(sum(cv.cost), 2)                          AS visitsCost,
                        count(CASE WHEN
                          ol.productId = cvhp.productId AND
                          ol.productVariantId = cvhp.productVariantId
                          THEN o.id
                              ELSE NULL END)                            AS pConversions #conversioni totali di questa visita per questo prodotto
                      FROM
                        Campaign c
                        JOIN CampaignVisit cv ON cv.campaignId = c.id
                        LEFT JOIN CampaignVisitHasProduct cvhp
                          ON cvhp.campaignId = cv.campaignId AND cvhp.campaignVisitId = cv.id
                        LEFT JOIN (CampaignVisitHasOrder cvho
                        JOIN `Order` o ON cvho.orderId = o.id
                        JOIN OrderLine ol ON o.id = ol.orderId
                        ) ON cvho.campaignVisitId = cv.id AND
                             cvho.campaignId = cv.campaignId
                      WHERE (timestamp BETWEEN ifnull(?, timestamp) AND ifnull(?, timestamp) OR
                             o.orderDate BETWEEN ifnull(?, o.orderDate) AND ifnull(?, o.orderDate))
                      GROUP BY cvhp.productId, cvhp.productVariantId, cvhp.campaignId
                     ";

    const SQL_SELECT_PRODUCT_MARKETPLACE_ACCOUNT_CATEGORY = "SELECT
           concat(`p`.`id`, '-', `p`.`productVariantId`)          AS `codice`,
           `p`.`id`                                               AS `productId`,
           `p`.`productVariantId`                                 AS `productVariantId`,
           `p`.`itemno`                                           AS `itemno`,
           concat(`pss`.`name`, `pss`.`year`)                     AS `season`,
           `pb`.`name`                                            AS `brand`,
           `p`.`creationDate`                                     AS `creationDate`,
           concat(`m`.`name`, ' - ', `ma`.`name`)                 AS `marketplaceAccountName`,
           concat(s.id,'-',`s`.`name`)                            AS `shop`,
           `s`.`id`                                               AS `shopId`,
           `mahp`.`marketplaceProductId`                          AS `marketplaceProductId`,
           `mahp`.`marketplaceId`                                 AS `marketplaceId`,
           `mahp`.`marketplaceAccountId`                          AS `marketplaceAccountId`,
           `mahp`.`fee`                                           AS `fee`,
           p.qty                                                  AS stock,
           ps.name as productStatus,
           if(mahp.isToWork = 1, 'sìsi', 'no')                    AS isToWork,
           if(mahp.hasError = 1, 'sìsi', 'no')                    AS hasError,
           if(mahp.isDeleted = 1, 'sìsi', 'no')                   AS isDeleted,
           round(ifnull(visits, 0))                               AS visits,
           round(ifnull(visitsCost,0))                                 AS visitsCost,
           round(ifnull(conversions, 0))                          AS conversions,
           round(ifnull(conversionsValue, 0))                     AS conversionsValue,
           round(ifnull(pConversions, 0))                         AS pConversions,
           round(ifnull(pConversionsValue, 0))                    AS pConversionsValue,
           ordersIds                                              AS ordersIds,
           phpc.productCategoryId                                 AS categories,
           if(p.isOnSale = 0, min(shp.price), min(shp.salePrice)) AS activePrice
         FROM `Product` `p`
           JOIN `ProductStatus` `ps` ON ((`p`.`productStatusId` = `ps`.`id`))
           JOIN `ShopHasProduct` `shp`
             ON (((`p`.`id` = `shp`.`productId`) AND (`p`.`productVariantId` = `shp`.`productVariantId`)))
           JOIN `Shop` `s` ON ((`s`.`id` = `shp`.`shopId`))
           JOIN `ProductSeason` `pss` ON ((`pss`.`id` = `p`.`productSeasonId`))
           JOIN `ProductBrand` `pb` ON ((`p`.`productBrandId` = `pb`.`id`))
           JOIN ProductHasProductCategory phpc
             ON (p.id = phpc.productId AND p.productVariantId = phpc.productVariantId)
           JOIN `MarketplaceAccountHasProduct` `mahp`
             ON (((`mahp`.`productId` = `p`.`id`) AND (`mahp`.`productVariantId` = `p`.`productVariantId`)))
           JOIN `MarketplaceAccount` `ma`
             ON (((`ma`.`marketplaceId` = `mahp`.`marketplaceId`) AND
                  (`ma`.`id` = `mahp`.`marketplaceAccountId`)))
           JOIN `Marketplace` `m` ON ((`m`.`id` = `ma`.`marketplaceId`))
           LEFT JOIN ( " . self::SQL_SELECT_CAMPAING_PRODUCT_STATISTIC_MARKETPLACE_ACCOUNT . " ) sql2
             ON sql2.productId = mahp.productId AND sql2.productVariantId = mahp.productVariantId AND
                sql2.marketplaceId = mahp.marketplaceId AND
                sql2.marketplaceAccountId = mahp.marketplaceAccountId
         WHERE
           ma.id = ? AND
           ma.marketplaceId = ?
         GROUP BY productId, productVariantId, marketplaceId, marketplaceAccountId, productCategoryId";

    const SQL_SELECT_MARKETPLACE_ACCOUNT_STATISTICS =
        "SELECT
          m.id                                            AS marketplaceId,
          ma.id                                           AS marketplaceAccountId,
          m.name                                          AS marketplace,
          ma.name                                         AS marketplaceAccount,
          c.id                                            AS campaignId,
          c.name                                          AS campaign,
          m.type                                          AS marketplaceType,
          (SELECT count(DISTINCT mahp.productId, mahp.productVariantId)
           FROM MarketplaceAccountHasProduct mahp
           WHERE ma.id = mahp.marketplaceAccountId AND
                 ma.marketplaceId = mahp.marketplaceId AND mahp.isDeleted = 0 AND
                 mahp.isToWork = 0 AND mahp.hasError = 0) AS productCount,
          count(distinct cv.id)                                    AS visits,
          round(sum(cv.cost), 2)                          AS cost,
          count(distinct o.id)                                     AS orders,
          sum(ifnull(o.netTotal, 0))                      AS orderTotal,
          group_concat(DISTINCT o.id)                     AS ordersIds
        FROM Marketplace m
          JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
          LEFT JOIN Campaign c ON c.marketplaceId = ma.marketplaceId AND c.marketplaceAccountId = ma.id
          LEFT JOIN CampaignVisit cv ON c.id = cv.campaignId
          LEFT JOIN (CampaignVisitHasOrder cvho
            JOIN `Order` o ON o.id = cvho.orderId) ON cv.campaignId = cvho.campaignId AND cv.id = cvho.campaignVisitId
        WHERE (
          isnull(c.id) OR (
            cv.timestamp BETWEEN ifnull(?, timestamp) AND ifnull(?, timestamp) OR
            o.orderDate BETWEEN ifnull(?, o.orderDate) AND ifnull(?, o.orderDate)))
        GROUP BY ma.id, ma.marketplaceId";

    const SQL_SELECT_MARKETPLACE_ACCOUNT_PRODUCT_CATEGORY_STATISTICS =
        "SELECT
                      Parent.id as category,
                      count(DISTINCT codice) as products,
                      sum(visits) as visits,
                      sum(visitsCost) as visitsCost,
                      sum(conversions) as conversions,
                      sum(conversionsValue) as conversionsValue,
                      sum(pConversions) as pConversions,
                      sum(pConversionsValue) as pConversionsValue
                    FROM
                      ProductCategory Child
                      JOIN
                      ProductCategory Parent ON Child.lft BETWEEN Parent.lft AND Parent.rght
                      JOIN ( ".self::SQL_SELECT_PRODUCT_MARKETPLACE_ACCOUNT_CATEGORY." ) sel3 ON Child.id = sel3.categories
                    GROUP BY Parent.id
                    ORDER BY Child.lft";
}