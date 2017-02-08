<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductCategory;

/**
 * Class CMarketplaceCategoryStatisticListAjaxController
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
class CMarketplaceCategoryStatisticListAjaxController extends AAjaxController
{
    use TMySQLTimestamp;

    public function get()
    {
        $marketplaceAccountId = $this->app->router->request()->getRequestData('MarketplaceAccount');
        $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);

        $query = "SELECT
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
                      JOIN (
                             SELECT
                               concat(`p`.`id`, '-', `p`.`productVariantId`)          AS `codice`,
                               `p`.`id`                                               AS `productId`,
                               `p`.`productVariantId`                                 AS `productVariantId`,
                               `p`.`itemno`                                           AS `itemno`,
                               concat(`pss`.`name`, `pss`.`year`)                     AS `season`,
                               `pb`.`name`                                            AS `brand`,
                               `p`.`creationDate`                                     AS `creationDate`,
                               concat(`m`.`name`, ' - ', `ma`.`name`)                 AS `marketplaceAccountName`,
                               `s`.`name`                                             AS `shop`,
                               `s`.`id`                                               AS `shopId`,
                               `mahp`.`marketplaceProductId`                          AS `marketplaceProductId`,
                               `mahp`.`marketplaceId`                                 AS `marketplaceId`,
                               `mahp`.`marketplaceAccountId`                          AS `marketplaceAccountId`,
                               `mahp`.`fee`                                           AS `fee`,
                               p.qty                                                  AS stock,
                               if(mahp.isToWork = 1, 'sìsi', 'no')                    AS isToWork,
                               if(mahp.hasError = 1, 'sìsi', 'no')                    AS hasError,
                               if(mahp.isDeleted = 1, 'sìsi', 'no')                   AS isDeleted,
                               round(ifnull(visits, 0))                               AS visits,
                               round(ifnull(visitsCost,0))                                 AS visitsCost,
                               round(ifnull(conversions, 0))                          AS conversions,
                               round(ifnull(conversionsValue, 0))                     AS conversionsValue,
                               round(ifnull(pConversions, 0))                         AS pConversions,
                               round(ifnull(pConversionsValue, 0))                    AS pConversionsValue,
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
                               LEFT JOIN (SELECT
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
                                         ) sql2
                                 ON sql2.productId = mahp.productId AND sql2.productVariantId = mahp.productVariantId AND
                                    sql2.marketplaceId = mahp.marketplaceId AND
                                    sql2.marketplaceAccountId = mahp.marketplaceAccountId
                             WHERE
                               ma.id = ? AND
                               ma.marketplaceId = ?
                             GROUP BY productId, productVariantId, marketplaceId, marketplaceAccountId, productCategoryId) sel3 ON Child.id = sel3.categories
                    GROUP BY Parent.id
                    ORDER BY Child.lft";

        $origin = "SELECT parent.name, COUNT(product.name)
                        FROM nested_category AS node ,
                                nested_category AS parent,
                                product
                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                                AND node.category_id = product.category_id
                        GROUP BY parent.name
                        ORDER BY node.lft";

        //IL PROBLEMA é IL DIOCANE DI TIMESTAMP CHE RIMANE NULL DI MERDA DI DIO
        $timeFrom = new \DateTime($this->app->router->request()->getRequestData('startDate').' 00:00:00');
        $timeTo = new \DateTime($this->app->router->request()->getRequestData('endDate').' 00:00:00');

        $timeFrom = $timeFrom ? $this->time($timeFrom->getTimestamp()) : null;
        $timeTo = $timeTo ? $this->time($timeTo->getTimestamp()) : null;
        $queryParameters = [$timeFrom, $timeTo,$timeFrom, $timeTo,$marketplaceAccount->id, $marketplaceAccount->marketplaceId ];

        $datatable = new CDataTables($query, ['category'], $_GET, true);

        $prodottiMarks = $this->app->dbAdapter->query($datatable->getQuery(false, true), array_merge($queryParameters, $datatable->getParams()))->fetchAll();
        $count = $this->app->repoFactory->create('ProductCategory')->em()->findCountBySql($datatable->getQuery(true), array_merge($queryParameters, $datatable->getParams()));
        $totalCount = $this->app->repoFactory->create('ProductCategory')->em()->findCountBySql($datatable->getQuery('full'), array_merge($queryParameters, $datatable->getParams()));

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($prodottiMarks as $values) {
            /** @var CProductCategory $productCategory */
            $productCategory = $this->app->repoFactory->create('ProductCategory')->findOne([$values['category']]);
            $row = $values;
            $row["DT_RowId"] = $productCategory->printId();
            $row['category'] = $productCategory->getLocalizedPath();
            if($row['conversionsValue'] == 0) $row['cos'] = 'NaN';
            else $row['cos'] = round($row['visitsCost'] / $row['conversionsValue'] * 100,2);
            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}