<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

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
class CProductCampaignStatisticsAjaxController extends AAjaxController
{

    public function get()
    {
        $datatable = new CDataTables('vProductStatisticsCampaing',['productId','productVariantId','campaignId'],$_GET);

        $prodottiCampaing = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        $count = $this->app->repoFactory->create('ProductStatistics')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('ProductStatistics')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach($prodottiCampaing as $val){
            $productStatistic = $this->app->repoFactory->create('ProductStatistics')->findOneBy($val);
            $row = [];
            $row["DT_RowId"] = $productStatistic->printId();
            $row['code'] = $productStatistic->product->printId();
            $row['shops'] = $productStatistic->product->getShops('<br>');
            $row['season'] = $productStatistic->product->productSeason->name;
            $row['brand'] = $productStatistic->product->productBrand->name;
            $row['categories'] = $productStatistic->product->getLocalizedProductCategories('<br>');
            $row['first'] = $val['first'];
            $row['last'] = $val['last'];
            $row['pageView'] = $val['pageView'];
            $row['campaign'] = $productStatistic->campaign->name;
            $row['firstest'] = $productStatistic->creationDate;

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}