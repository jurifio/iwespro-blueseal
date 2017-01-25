<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;

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
class CMarketplaceAccountListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT
                  m.id                                                                                                      AS marketplaceId,
                  ma.id                                                                                                     AS marketplaceAccountId,
                  m.name                                                                                                    AS marketplace,
                  ma.name                                                                                                   AS marketplaceAccount,
                  c.id                                                                                                      AS campaignId,
                  c.name                                                                                                    AS campaign,
                  m.type                                                                                                    AS marketplaceType,
                  count(DISTINCT mahp.productVariantId)                                                                     AS productCount,
                  (SELECT count(*)
                   FROM CampaignVisit cv
                   WHERE cv.campaignId = c.id AND cv.timestamp BETWEEN ifnull(?, cv.timestamp) AND ifnull(?, cv.timestamp)) AS visits,
                  round((SELECT sum(mahp1.fee)
                   FROM CampaignVisit cv
                     JOIN CampaignVisitHasProduct cvhp ON cv.id = cvhp.campaignVisitId AND cv.campaignId = cvhp.campaignId
                     JOIN MarketplaceAccountHasProduct mahp1
                       ON mahp1.productId = cvhp.productId AND cvhp.productVariantId = mahp1.productVariantId
                   WHERE cvhp.campaignId = c.id AND mahp1.marketplaceAccountId = mahp.marketplaceAccountId AND
                         cv.timestamp BETWEEN ifnull(?, cv.timestamp) AND ifnull(?, cv.timestamp)))                          AS cost,
                  (SELECT count(DISTINCT cvho.orderId)
                   FROM CampaignVisitHasOrder cvho
                     JOIN `Order` o ON o.id = cvho.orderId
                   WHERE cvho.campaignId = c.id AND o.orderDate BETWEEN ifnull(?, o.orderDate) AND ifnull(?, o.orderDate))  AS orders,
                   (SELECT group_concat(DISTINCT cvho.orderId)
                   FROM CampaignVisitHasOrder cvho
                     JOIN `Order` o ON o.id = cvho.orderId
                   WHERE cvho.campaignId = c.id AND o.orderDate BETWEEN ifnull(?, o.orderDate) AND ifnull(?, o.orderDate))  AS ordersIds,
                  (SELECT sum(o.netTotal)
                   FROM CampaignVisitHasOrder cvho
                     JOIN `Order` o ON o.id = cvho.orderId
                   WHERE cvho.campaignId = c.id AND o.orderDate BETWEEN ifnull(?, o.orderDate) AND ifnull(?, o.orderDate))  AS orderTotal
                FROM Marketplace m
                  JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
                  LEFT JOIN MarketplaceAccountHasProduct mahp ON ma.id = mahp.marketplaceAccountId AND
                                                                 ma.marketplaceId = mahp.marketplaceId AND mahp.isDeleted = 0 AND
                                                                 mahp.isToWork = 0 AND mahp.hasError = 0
                  LEFT JOIN Campaign c ON c.code = concat('MarketplaceAccount', ma.id, '-', ma.marketplaceId)
                GROUP BY ma.id, ma.marketplaceId";

        $datatable = new CDataTables($sql, ['marketplaceId', 'marketplaceAccountId', 'campaignId'], $_GET, true);

        $timeFrom = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('startDate'));
        $timeTo = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('endDate'));
        $timeFrom = $timeFrom ? $timeFrom->format('Y-m-d') : null;
        $timeTo = $timeTo ? $timeTo->format('Y-m-d') : null;
        $params = array_merge([$timeFrom,$timeTo,$timeFrom,$timeTo,$timeFrom,$timeTo,$timeFrom,$timeTo,$timeFrom,$timeTo],$datatable->getParams());
        $marketplaceAccounts = $this->app->dbAdapter->query($datatable->getQuery(false, true), $params)->fetchAll();
        $count = $this->app->repoFactory->create('CampaingVisitHasProduct')->em()->findCountBySql($datatable->getQuery(true), $params);
        $totalCount = $this->app->repoFactory->create('CampaingVisitHasProduct')->em()->findCountBySql($datatable->getQuery('full'), $params);

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['query'] = $datatable->getQuery(false, true);
        $response ['params'] = $params;
        $response ['data'] = [];

        foreach ($marketplaceAccounts as $val) {
            $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneBy(['marketplaceId'=>$val['marketplaceId'],'id'=>$val['marketplaceAccountId']]);
            $row = [];
            $row["DT_RowId"] = $marketplaceAccount->printId();
            $row['code'] = $marketplaceAccount->printId();
            $row['marketplace'] = $marketplaceAccount->marketplace->name;
            $row['marketplaceAccount'] = '<a href="/blueseal/prodotti/marketplace/account/'.$marketplaceAccount->printId().'">'.$marketplaceAccount->name.'</a>';
            $row['marketplaceType'] = $marketplaceAccount->marketplace->type;
            $row['productCount'] = $val['productCount'];
            $row['visits'] = $val['visits'];
            $row['orders'] = $val['orders'];
            $row['ordersIds'] = $val['ordersIds'];
            $row['cost'] = $val['cost'];
            $row['orderTotal'] = $val['orderTotal'];
            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}