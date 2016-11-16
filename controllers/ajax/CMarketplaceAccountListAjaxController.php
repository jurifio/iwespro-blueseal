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
        $sql = "SELECT  m.id as marketplaceId,
                        ma.id as marketplaceAccountId,
                        m.name as marketplace,
                        ma.name as marketplaceAccount,
                        c.id as campaignId,
                        c.name as campaign,
                        m.type as marketplaceType,
                        count(distinct mahp.productVariantId) AS productCount,
                        (SELECT count(DISTINCT cv.id) FROM CampaignVisit cv WHERE cv.campaignId = c.id) AS visits,
                        (SELECT count(distinct cvho.orderId) FROM CampaignVisitHasOrder cvho WHERE cvho.campaignId = c.id) as orders
                FROM Marketplace m
                  JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
                  LEFT JOIN MarketplaceAccountHasProduct mahp ON ma.id = mahp.marketplaceAccountId AND ma.marketplaceId = mahp.marketplaceId and mahp.isDeleted = 0 and mahp.isToWork = 0 and mahp.hasError = 0
                  LEFT JOIN Campaign c ON c.code = concat('MarketplaceAccount',ma.id,'-',ma.marketplaceId) GROUP BY ma.id, ma.marketplaceId";

        $datatable = new CDataTables($sql, ['marketplaceId', 'marketplaceAccountId', 'campaignId'], $_GET, true);

        $marketplaceAccounts = $this->app->dbAdapter->query($datatable->getQuery(false, true), $datatable->getParams())->fetchAll();
        $count = $this->app->repoFactory->create('CampaingVisitHasProduct')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('CampaingVisitHasProduct')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
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
            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}