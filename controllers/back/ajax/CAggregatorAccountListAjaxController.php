<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;

/**
 * Class CMarketplaceAccountListAjaxController
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
        $sql="SELECT
          m.id                                            AS marketplaceId,
          ma.id                                           AS marketplaceAccountId,
          m.name                                          AS marketplace,
          ma.name                                         AS marketplaceAccount,
         m.type as marketplaceType,
          if(ma.isActive=0,'si','no') as isActive 
        FROM Marketplace m
          JOIN MarketplaceAccount ma ON m.id = ma.marketplaceId
        where m.type='cpc'
        GROUP BY ma.id, ma.marketplaceId";

        $datatable = new CDataTables($sql, ['marketplaceId', 'marketplaceAccountId'], $_GET, true);

        $timeFrom = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('startDate'));
        $timeTo = \DateTime::createFromFormat('Y-m-d', $this->app->router->request()->getRequestData('endDate'));
        $timeFrom = $timeFrom ? $timeFrom->format('Y-m-d') : null;
        $timeTo = $timeTo ? $timeTo->format('Y-m-d') : null;
        $params = array_merge([$timeFrom, $timeTo, $timeFrom, $timeTo], $datatable->getParams());
        $marketplaceAccounts = $this->app->dbAdapter->query($datatable->getQuery(false, true), $params)->fetchAll();


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['query'] = $datatable->getQuery(false, true);
        $response ['params'] = $params;
        $response ['data'] = [];

        foreach ($marketplaceAccounts as $val) {
            if($val['marketplaceType']=='cpc') {
            $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['marketplaceId' => $val['marketplaceId'], 'id' => $val['marketplaceAccountId']]);

                $row = [];
                $row["DT_RowId"] = $marketplaceAccount->printId();
                $row['code'] = $marketplaceAccount->printId();
                $row['marketplace'] = $marketplaceAccount->marketplace->name;
                $row['marketplaceAccount'] = '<a href="/blueseal/prodotti/marketplace/account/' . $marketplaceAccount->printId() . '">' . $marketplaceAccount->name . '</a>';
                $row['marketplaceType'] = $marketplaceAccount->marketplace->type;
                $response['data'][] = $row;
            }else{
                continue;
            }
        }

        return json_encode($response);
    }
}