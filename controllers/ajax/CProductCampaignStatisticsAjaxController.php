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
class CProductCampaignStatisticsAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "select concat(`psd`.`productId`,'-',`psd`.`productVariantId`) AS `code`,`psd`.`productId` AS `productId`,`psd`.`productVariantId` AS `productVariantId`,`psd`.`campaignId` AS `campaignId`,group_concat(distinct `s`.`name` separator ',') AS `shops`,`pse`.`name` AS `season`,`c`.`name` AS `campaign`,count(`psd`.`id`) AS `pageView`,count(distinct `psdhol`.`productStatisticsDetailId`) AS `conversions`,min(`psd`.`timestamp`) AS `first`,max(`psd`.`timestamp`) AS `last`,`ps`.`creationDate` AS `firstest`,`pb`.`name` AS `brand` from (((((((((((`Product` `p` join `ProductBrand` `pb`) join `ProductSeason` `pse`) join `ProductHasProductCategory` `phpc`) join `ProductCategory` `pc`) join `ShopHasProduct` `shp`) join `Shop` `s`) join `ProductStatistics` `ps`) join `ProductStatisticsDetail` `psd`) join `Campaign` `c`) left join `ProductStatisticsDetailHasOrderLine` `psdhol` on((`psd`.`id` = `psdhol`.`productStatisticsDetailId`))) left join `OrderLine` `ol` on(((`psdhol`.`orderLineId` = `ol`.`id`) and (`psdhol`.`orderId` = `ol`.`orderId`)))) where ((`p`.`productBrandId` = `pb`.`id`) and (`p`.`id` = `phpc`.`productId`) and (`p`.`productVariantId` = `phpc`.`productVariantId`) and (`phpc`.`productCategoryId` = `pc`.`id`) and (`p`.`productSeasonId` = `pse`.`id`) and (`p`.`id` = `shp`.`productId`) and (`p`.`productVariantId` = `shp`.`productVariantId`) and (`s`.`id` = `shp`.`shopId`) and (`p`.`id` = `psd`.`productId`) and (`p`.`productVariantId` = `psd`.`productVariantId`) and (`p`.`id` = `ps`.`productId`) and (`p`.`productVariantId` = `ps`.`productVariantId`) and (`ps`.`campaignId` = `psd`.`campaignId`) and (`psd`.`campaignId` = `c`.`id`)) group by `psd`.`productId`,`psd`.`productVariantId`,`psd`.`campaignId`";
        $datatable = new CDataTables($sql,['productId','productVariantId','campaignId'],$_GET,true);

        $prodottiCampaing = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        $count = $this->app->repoFactory->create('CampaingVisitHasProduct')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('CampaingVisitHasProduct')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach($prodottiCampaing as $val){
            $productStatistic = $this->app->repoFactory->create('CampaingVisitHasProduct')->findOneBy($val);
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
            $row['conversions'] = $val['conversions'];
            $row['campaign'] = $productStatistic->campaign->name;
            $row['firstest'] = $productStatistic->creationDate;

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}