<?php
namespace bamboo\controllers\back\ajax;

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
                  m.id                                                                                                       AS marketplaceId,
                  ma.id                                                                                                      AS marketplaceAccountId,
                  m.name                                                                                                     AS marketplace,
                  ma.name                                                                                                    AS marketplaceAccount,
                  c.id                                                                                                       AS campaignId,
                  c.name                                                                                                     AS campaign,
                  m.type                                                                                                     AS marketplaceType,
                  (SELECT count(DISTINCT mahp.productId,mahp.productVariantId) 
                    FROM MarketplaceAccountHasProduct mahp 
                    WHERE ma.id = mahp.marketplaceAccountId AND
                          ma.marketplaceId = mahp.marketplaceId AND mahp.isDeleted = 0 AND
                          mahp.isToWork = 0 AND mahp.hasError = 0)                                                           AS productCount,
                  count(cv.id)                                                                                               AS visits,
                  round(sum(cv.cost),2)                                                                                               AS cost,
                  count(o.id)                                                                                                AS orders,
                  sum(ifnull(o.netTotal,0))  AS orderTotal,
                  group_concat(DISTINCT o.id) AS ordersIds
                FROM Marketplace m
                  JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
                  JOIN Campaign c ON c.marketplaceId = ma.marketplaceId and c.marketplaceAccountId = ma.id
                  LEFT JOIN CampaignVisit cv ON c.id = cv.campaignId
                  LEFT JOIN (CampaignVisitHasOrder cvho JOIN `Order` o ON o.id = cvho.orderId) ON cv.campaignId = cvho.campaignId AND cv.id = cvho.campaignVisitId
                  WHERE (
                    cv.timestamp BETWEEN ifnull(?,timestamp) AND ifnull(?,timestamp) OR 
                    o.orderDate BETWEEN ifnull(?,o.orderDate) AND ifnull(?,o.orderDate) )
                GROUP BY ma.id, ma.marketplaceId";

        $datatable = new CDataTables($sql, ['marketplaceId', 'marketplaceAccountId', 'campaignId'], $_GET, true);

        $timeFrom = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('startDate'));
        $timeTo = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('endDate'));
        $timeFrom = $timeFrom ? $timeFrom->format('Y-m-d') : null;
        $timeTo = $timeTo ? $timeTo->format('Y-m-d') : null;
        $params = array_merge([$timeFrom, $timeTo, $timeFrom, $timeTo], $datatable->getParams());
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
            $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneBy(['marketplaceId' => $val['marketplaceId'], 'id' => $val['marketplaceAccountId']]);
            $row = [];
            $row["DT_RowId"] = $marketplaceAccount->printId();
            $row['code'] = $marketplaceAccount->printId();
            $row['marketplace'] = $marketplaceAccount->marketplace->name;
            $row['marketplaceAccount'] = '<a href="/blueseal/prodotti/marketplace/account/' . $marketplaceAccount->printId() . '">' . $marketplaceAccount->name . '</a>';
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