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
class CMarketplaceAccountListAjaxController extends AMarketplaceAccountAjaxController
{

    public function get()
    {

        $datatable = new CDataTables(self::SQL_SELECT_MARKETPLACE_ACCOUNT_STATISTICS, ['marketplaceId', 'marketplaceAccountId', 'campaignId'], $_GET, true);

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