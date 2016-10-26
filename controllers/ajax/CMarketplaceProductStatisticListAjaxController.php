<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

/**
 * Class CMarketplaceProductStatisticListAjaxController
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
class CMarketplaceProductStatisticListAjaxController extends AAjaxController
{
    public function get()
    {
        $sample = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->getEmptyEntity();


        $query = "SELECT
        concat(`p`.`id`, '-', `p`.`productVariantId`)           AS `code`,
        `p`.`id`                                                AS `productId`,
        `p`.`productVariantId`                                  AS `productVariantId`,
        `p`.`itemno`                                            AS `itemno`,
        concat(`pss`.`name`, `pss`.`year`)                      AS `season`,
        `pb`.`name`                                             AS `brand`,
        `p`.`creationDate`                                      AS `creationDate`,
        concat(`m`.`name`, `ma`.`name`) AS `marketplaceAccountName`,
        `s`.`name`                       AS `shop`,
        `s`.`id`                         AS `shopId`,
        `mahp`.`marketplaceProductId`                           AS `marketplaceProductId`,
        `mahp`.`marketplaceId`                                  AS `marketplaceId`,
        `mahp`.`marketplaceAccountId`                           AS `marketplaceAccountId`,
        `mahp`.`fee`                                       AS `fee`,
        psd.timestamp                                           AS visitTimestamp,
        psd.id                                                  AS visitId,
        pst.pageView                                            AS visits,
        pst.conversion                                          AS conversions,
        c.code                                                  AS campaignCode
      FROM ((((((((`Product` `p`
        JOIN `ProductStatus` `ps` ON ((`p`.`productStatusId` = `ps`.`id`)))
        JOIN `ShopHasProduct` `shp` ON (((`p`.`id` = `shp`.`productId`) AND (`p`.`productVariantId` = `shp`.`productVariantId`))))
        JOIN `Shop` `s` ON ((`s`.`id` = `shp`.`shopId`)))
        JOIN `ProductSeason` `pss` ON ((`pss`.`id` = `p`.`productSeasonId`)))
        JOIN `ProductBrand` `pb` ON ((`p`.`productBrandId` = `pb`.`id`)))
        JOIN `MarketplaceAccountHasProduct` `mahp` ON (((`mahp`.`productId` = `p`.`id`) AND (`mahp`.`productVariantId` = `p`.`productVariantId`))))
        JOIN `MarketplaceAccount` `ma` ON (((`ma`.`marketplaceId` = `mahp`.`marketplaceId`) AND (`ma`.`id` = `mahp`.`marketplaceAccountId`))))
        JOIN `Marketplace` `m` ON ((`m`.`id` = `ma`.`marketplaceId`)))
        LEFT JOIN Campaign c ON c.code = concat('MarketplaceAccount', ma.id, '-', ma.marketplaceId)
        LEFT JOIN ProductStatistics pst ON c.id = pst.campaignId AND pst.productId = p.id AND pst.productVariantId = p.productVariantId
        LEFT JOIN ProductStatisticsDetail psd ON c.id = psd.campaignId AND psd.productId = p.id AND psd.productVariantId = p.productVariantId
        LEFT JOIN ProductStatisticsDetailHasOrderLine psdhol on psd.id = psdhol.productStatisticsDetailId
      WHERE (((`ps`.`isReady` = 1) AND (`p`.`qty` > 0)) OR (`m`.`id` IS NOT NULL))
          AND timestamp >= ifnull(?, timestamp) 
          AND timestamp <= ifnull(?, timestamp)";

        $params = $_GET;

        $datatable = new CDataTables("(".$query.")", $sample->getPrimaryKeys(), $_GET);
        $a = $this->app->router->request()->getRequestData();
        $marketplaceAccountId = $this->app->router->request()->getRequestData('accountId');
        $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);
        $datatable->addCondition('marketplaceId', [$marketplaceAccount->marketplaceId]);
        $datatable->addCondition('marketplaceAccountId', [$marketplaceAccount->id]);

        $datatable->addCondition('shopId', $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $datatable->addSearchColumn('marketplaceProductId');


        $prodottiMarks = $sample->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;
        foreach ($prodottiMarks as $prodottiMark) {

            $val = $prodottiMark->product;
            /** @var CProduct $val*/
            $img = $val->getDummyPictureUrl();
            if ($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $shops = [];
            foreach ($val->shop as $shop) {
                $shops[] = $shop->name;
            }

            $response['data'][$i]["DT_RowId"] = $val->printId();
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>';
            $response['data'][$i]['brand'] = $val->productBrand->name;
            $response['data'][$i]['season'] = $val->productSeason->name;

            $th = "";
            $tr = "";
            $res = $this->app->dbAdapter->query("SELECT s.name, sum(ps.stockQty) stock
                                          FROM ProductSku ps , ProductSize s
                                          WHERE ps.productSizeId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productSizeId
                                          HAVING stock > 0 ORDER BY `name`", [$val->id, $val->productVariantId])->fetchAll();
            foreach ($res as $sums) {
                $th .= "<th>" . $sums['name'] . "</th>";
                $tr .= "<td>" . $sums['stock'] . "</td>";
            }
            $response['data'][$i]["stock"] = '<table class="nested-table"><thead><tr>' . $th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $response['data'][$i]['shop'] = implode(', ', $shops);
            $response['data'][$i]['dummy'] = '<img width="50" src="' . $img . '" />' . $imgs . '<br />';
            $response['data'][$i]['itemno'] = '<span class="small">';
            $response['data'][$i]['itemno'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $response['data'][$i]['itemno'] .= '</span>';

            $response['data'][$i]['fee'] = 0;
            $marketplaces = [];
            foreach ($val->marketplaceAccountHasProduct as $mProduct) {
                if ($marketplaceAccount &&
                    ($marketplaceAccount->id != $mProduct->marketplaceAccountId ||
                        $marketplaceAccount->marketplaceId != $mProduct->marketplaceId)) continue;
                $style = $mProduct->isToWork == 0 ? ($mProduct->hasError ? 'style="color:red"' : 'style="color:green"') : "";
                $marketplaces[] = '<span ' . $style . '>' . $mProduct->marketplaceAccount->marketplace->name . ' - ' . $mProduct->marketplaceAccount->name . ( empty ($mProduct->marketplaceProductId) ? "" : ' (' . $mProduct->marketplaceProductId . ')</span>' );
                $response['data'][$i]['fee'] += $mProduct->fee;
            }

            $response['data'][$i]['marketplaceAccountName'] = implode('<br>', $marketplaces);

            $response['data'][$i]['categories'] = $val->productCategory->getFirst()->getLocalizedName();

            $i++;
        }

        return json_encode($response);
    }
}