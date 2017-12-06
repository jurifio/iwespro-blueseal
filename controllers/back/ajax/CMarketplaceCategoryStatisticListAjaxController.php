<?php
namespace bamboo\controllers\back\ajax;

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
class CMarketplaceCategoryStatisticListAjaxController extends AMarketplaceAccountAjaxController
{
    use TMySQLTimestamp;

    public function get()
    {
        $marketplaceAccountId = $this->app->router->request()->getRequestData('MarketplaceAccount');
        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);

        //IL PROBLEMA é IL DIOCANE DI TIMESTAMP CHE RIMANE NULL DI MERDA DI DIO
        $timeFrom = new \DateTime($this->app->router->request()->getRequestData('startDate').' 00:00:00');
        $timeTo = new \DateTime($this->app->router->request()->getRequestData('endDate').' 00:00:00');

        $timeFrom = $timeFrom ? $this->time($timeFrom->getTimestamp()) : null;
        $timeTo = $timeTo ? $this->time($timeTo->getTimestamp()) : null;
        $queryParameters = [$timeFrom, $timeTo,$timeFrom, $timeTo,$marketplaceAccount->id, $marketplaceAccount->marketplaceId ];

        $datatable = new CDataTables(self::SQL_SELECT_MARKETPLACE_ACCOUNT_PRODUCT_CATEGORY_STATISTICS, ['category'], $_GET, true);

        $prodottiMarks = $this->app->dbAdapter->query($datatable->getQuery(false, true), array_merge($queryParameters, $datatable->getParams()))->fetchAll();
        $count = \Monkey::app()->repoFactory->create('ProductCategory')->em()->findCountBySql($datatable->getQuery(true), array_merge($queryParameters, $datatable->getParams()));
        $totalCount = \Monkey::app()->repoFactory->create('ProductCategory')->em()->findCountBySql($datatable->getQuery('full'), array_merge($queryParameters, $datatable->getParams()));

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($prodottiMarks as $values) {
            /** @var CProductCategory $productCategory */
            $productCategory = \Monkey::app()->repoFactory->create('ProductCategory')->findOne([$values['category']]);
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