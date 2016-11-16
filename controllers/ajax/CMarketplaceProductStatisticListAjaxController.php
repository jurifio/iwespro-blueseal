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

        $marketplaceAccountId = $this->app->router->request()->getRequestData('MarketplaceAccount');
        $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);
        $campaign = \Monkey::app()->repoFactory->create('Campaign')->readCampaignCode($marketplaceAccount->getCampaignCode());

        $query = "SELECT
                      concat(`p`.`id`, '-', `p`.`productVariantId`) AS `codice`,
                      `p`.`id`                                      AS `productId`,
                      `p`.`productVariantId`                        AS `productVariantId`,
                      `p`.`itemno`                                  AS `itemno`,
                      concat(`pss`.`name`, `pss`.`year`)            AS `season`,
                      `pb`.`name`                                   AS `brand`,
                      `p`.`creationDate`                            AS `creationDate`,
                      concat(`m`.`name`, ' - ', `ma`.`name`)        AS `marketplaceAccountName`,
                      `s`.`name`                                    AS `shop`,
                      `s`.`id`                                      AS `shopId`,
                      `mahp`.`marketplaceProductId`                 AS `marketplaceProductId`,
                      `mahp`.`marketplaceId`                        AS `marketplaceId`,
                      `mahp`.`marketplaceAccountId`                 AS `marketplaceAccountId`,
                      `mahp`.`fee`                                  AS `fee`,
                      mahp.isToWork,
                      mahp.hasError,
                      mahp.isDeleted,
                      cv.timestamp                                  AS visitTimestamp,                  
                      cv.id                                         AS visitId,
                      count(distinct cv.id)                          AS visits,
                      count(distinct cvho.orderId)                   AS conversions,
                      ifnull(c.code, '')                            AS campaignCode
                    FROM `Product` `p`
                      JOIN `ProductStatus` `ps` ON ((`p`.`productStatusId` = `ps`.`id`))
                      JOIN `ShopHasProduct` `shp`
                        ON (((`p`.`id` = `shp`.`productId`) AND (`p`.`productVariantId` = `shp`.`productVariantId`)))
                      JOIN `Shop` `s` ON ((`s`.`id` = `shp`.`shopId`))
                      JOIN `ProductSeason` `pss` ON ((`pss`.`id` = `p`.`productSeasonId`))
                      JOIN `ProductBrand` `pb` ON ((`p`.`productBrandId` = `pb`.`id`))
                      JOIN `MarketplaceAccountHasProduct` `mahp`
                        ON (((`mahp`.`productId` = `p`.`id`) AND (`mahp`.`productVariantId` = `p`.`productVariantId`)))
                      JOIN `MarketplaceAccount` `ma`
                        ON (((`ma`.`marketplaceId` = `mahp`.`marketplaceId`) AND (`ma`.`id` = `mahp`.`marketplaceAccountId`)))
                      JOIN `Marketplace` `m` ON ((`m`.`id` = `ma`.`marketplaceId`))
                      LEFT JOIN (Campaign c
                        JOIN CampaignVisit cv ON c.id = cv.campaignId
                        JOIN CampaignVisitHasProduct cvhp ON cv.campaignId = cvhp.campaignId AND cv.id = cvhp.campaignVisitId)
                        ON cvhp.productId = `p`.id AND cvhp.productVariantId = `p`.productVariantId
                      LEFT JOIN (CampaignVisitHasOrder cvho
                        JOIN OrderLine ol
                          ON cvho.orderId = ol.orderId)
                        ON ol.productId = p.id AND ol.productVariantId = p.productVariantId AND cv.campaignId = cvho.campaignId AND
                           cvhp.campaignVisitId = cvho.campaignVisitId
                    WHERE ma.id = ? AND ma.marketplaceId = ? AND c.id = ? AND
                        (((`ps`.`isReady` = 1) AND (`p`.`qty` > 0)) OR (`m`.`id` IS NOT NULL))
                          AND timestamp >= ifnull(?, timestamp)
                          AND timestamp <= ifnull(?, timestamp) 
                    GROUP BY productId, productVariantId";

        $timeFrom = $this->app->router->request()->getRequestData('startDate') ?? null;
        $timeTo = $this->app->router->request()->getRequestData('endDate') ?? null;
        $queryParameters = [$marketplaceAccount->id, $marketplaceAccount->marketplaceId, $campaign->id, $timeFrom, $timeTo];
        $datatable = new CDataTables($query, $sample->getPrimaryKeys(), $_GET, true);
        $datatable->addCondition('shopId', $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $datatable->addSearchColumn('marketplaceProductId');

        $prodottiMarks = $this->app->dbAdapter->query($datatable->getQuery(false, true), array_merge($queryParameters, $datatable->getParams()))->fetchAll();
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), array_merge($queryParameters, $datatable->getParams()));
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), array_merge($queryParameters, $datatable->getParams()));

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($prodottiMarks as $values) {

            $row = [];
            $prodottiMark = $this->app->repoFactory->create('MarketplaceAccountHasProduct')->findOneBy([
                'marketplaceId' => $values['marketplaceId'],
                'marketplaceAccountId' => $values['marketplaceAccountId'],
                'productId' => $values['productId'],
                'productVariantId' => $values['productVariantId'],
            ]);
            /** @var CProduct $val */
            $val = $prodottiMark->product;

            $img = $val->getDummyPictureUrl();
            if ($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';
            $row['codice'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->printId() . '</a>';
            $row['marketCode'] = $prodottiMark->printId();
            $row['brand'] = $val->productBrand->name;
            $row['season'] = $val->productSeason->name;

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
            $row["stock"] = '<table class="nested-table"><thead><tr>' . $th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $row['shop'] = $val->getShops('<br>');
            $row['dummy'] = '<img width="50" src="' . $img . '" />' . $imgs . '<br />';
            $row['itemno'] = '<span class="small">';
            $row['itemno'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $row['itemno'] .= '</span>';


            $row['fee'] = $prodottiMark->fee;
            $row['isToWork'] = $prodottiMark->isToWork ? 'sì' : 'no';
            $row['hasError'] = $prodottiMark->hasError ? 'sì' : 'no';
            $row['isDeleted'] = $prodottiMark->isDeleted ? 'sì' : 'no';
            $row['marketplaceAccountName'] = $prodottiMark->marketplaceAccount->marketplace->name;
            $row['creationDate'] = $val->creationDate;
            $row['categories'] = $val->getLocalizedProductCategories("<br>");
            $row['conversions'] = $values['conversions'];
            $row['visits'] = $values['visits'];

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}