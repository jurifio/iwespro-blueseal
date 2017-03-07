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
class CMarketplaceCategoryStatisticListAjaxController extends AMarketplaceAccountAjaxController
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
                      JOIN ( ".self::SQL_SELECT_CAMPAING_PRODUCT_STATISTIC_MARKETPLACE_ACCOUNT." ) sel3 ON Child.id = sel3.categories
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