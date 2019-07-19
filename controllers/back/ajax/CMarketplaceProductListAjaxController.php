<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;

/**
 * Class CMarketplaceProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2016
 * @since 1.0
 */
class CMarketplaceProductListAjaxController extends AAjaxController
{
    public function get()
    {

        $sample = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct')->getEmptyEntity();
        $sql = "select concat(`p`.`id`,'-',`p`.`productVariantId`) AS `code`,
                              `p`.`id` AS `productId`,
                              `p`.`productVariantId` AS `productVariantId`,
                              `p`.`itemno` AS `itemno`,concat(`pss`.`name`,`pss`.`year`) AS `season`,
                               `pb`.`name` AS `brand`,if(`p`.`qty`,'sì','no') AS `stock`,
                                `p`.`creationDate` AS `creationDate`,
                                 concat(ifnull(`m`.`name`,''),' - ',ifnull(`ma`.`name`,'')) AS `marketplaceAccountName`,
                                `s`.`name` AS `shop`,
                                `s`.`id` AS `shopId`,
                                '' as hasEan,
                                `mahp`.`marketplaceProductId` AS `marketplaceProductId`,
                                `mahp`.`marketplaceId` AS `marketplaceId`,
                                `mahp`.`marketplaceAccountId` AS `marketplaceAccountId`,
                                `mahp`.`fee` AS `fee`,
                                `phpc`.`productCategoryId` AS `category`,
                                 if(isnull(`mahp`.`marketplaceAccountId`),
                                '',
                               concat_ws(',',if((`mahp`.`isToWork` = 0),'lavorato',''),if((`mahp`.`hasError` = 1),'errore',''),if((`mahp`.`isDeleted` = 1),'cancellato',''))) AS `status`
                              from ((((((((`Product` `p` join 
                                  `ProductStatus` `ps` on((`p`.`productStatusId` = `ps`.`id`)))
                                  join `ShopHasProduct` `shp` on(((`p`.`id` = `shp`.`productId`) and (`p`.`productVariantId` = `shp`.`productVariantId`))))
                                  join `Shop` `s` on((`s`.`id` = `shp`.`shopId`))) 
                                  join `ProductSeason` `pss` on((`pss`.`id` = `p`.`productSeasonId`)))
                                  join `ProductBrand` `pb` on((`p`.`productBrandId` = `pb`.`id`))) 
                                  join `ProductHasProductPhoto` `phpp` on(((`p`.`id` = `phpp`.`productId`) and (`p`.`productVariantId` = `phpp`.`productVariantId`)))) 
                                  join `ProductHasProductCategory` `phpc` on(((`p`.`id` = `phpc`.`productId`) and (`p`.`productVariantId` = `phpc`.`productVariantId`)))) 
                                  left join ((`MarketplaceAccountHasProduct` `mahp` 
                                      join `MarketplaceAccount` `ma` on(((`ma`.`marketplaceId` = `mahp`.`marketplaceId`) and (`ma`.`id` = `mahp`.`marketplaceAccountId`)))) 
                                      join `Marketplace` `m` on((`m`.`id` = `ma`.`marketplaceId`))) on(((`mahp`.`productId` = `p`.`id`) and (`mahp`.`productVariantId` = `p`.`productVariantId`)))) 
                                where (((`ps`.`isReady` = 1) and (`p`.`qty` > 0)) or (`m`.`id` is not null))";
        $datatable = new CDataTables($sql, $sample->getPrimaryKeys(), $_GET,true);

        $datatable->addCondition('shopId', \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $datatable->addSearchColumn('marketplaceProductId');

        $righe = $this->app->dbAdapter->query($datatable->getQuery(), $datatable->getParams())->fetchAll();
        $count = $sample->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $sample->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($righe as $val) {
            $row = [];
            $marketplaceHasProduct = \Monkey::app()->repoFactory->create('MarketplaceAccountHasProduct')->findOne($val);
            if (is_null($marketplaceHasProduct)) {
                $product = \Monkey::app()->repoFactory->create('Product')->findOne([$val['productId'], $val['productVariantId']]);

                $row['fee'] = 0;
                $row['marketplaceAccountName'] = "";
            } else {
                $product = $marketplaceHasProduct->product;

                $style = $marketplaceHasProduct->isToWork == 0 ? ($marketplaceHasProduct->hasError ? 'style="color:red"' : 'style="color:green"') : "";
                $row['marketplaceAccountName'] = '<span ' . $style . '>' .
                    $marketplaceHasProduct->marketplaceAccount->marketplace->name . ' - ' .
                    $marketplaceHasProduct->marketplaceAccount->name .
                    (empty ($marketplaceHasProduct->marketplaceProductId) ? "" : ' (' . $marketplaceHasProduct->marketplaceProductId . ')</span>');
                $row['status'] = $marketplaceHasProduct->isToWork == 0 ? "lavorato" : "".",<br>".
                                    $marketplaceHasProduct->hasError == 1 ? "errore" : "".",<br>".
                                    $marketplaceHasProduct->isDeleted == 1 ? "cancellato" : "";
                $row['fee'] = $marketplaceHasProduct->fee;
            }
            /** @var CProduct $product */
            if ($product->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $shops = [];
            foreach ($product->shop as $shop) {
                $shops[] = $shop->name;
            }

            $row["DT_RowId"] = $product->printId();
            $row["DT_RowClass"] = 'colore';
            $row['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/prodotti/modifica?id=' . $product->id . '&productVariantId=' . $product->productVariantId . '">' . $product->id . '-' . $product->productVariantId . '</a>';
            $row['brand'] = $product->productBrand->name;
            $row['season'] = $product->productSeason->name;

            $th = "";
            $tr = "";
            $res = $this->app->dbAdapter->query("SELECT s.name, sum(ps.stockQty) stock
                                          FROM ProductSku ps , ProductSize s
                                          WHERE ps.productSizeId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productSizeId
                                          HAVING stock > 0 ORDER BY `name`", [$product->id, $product->productVariantId])->fetchAll();
            foreach ($res as $sums) {
                $th .= "<th>" . $sums['name'] . "</th>";
                $tr .= "<td>" . $sums['stock'] . "</td>";
            }
            $row["stock"] = '<table class="nested-table"><thead><tr>' . $th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $row['shop'] = implode(', ', $shops);
            $row['dummy'] = '<img width="50" src="' . $product->getDummyPictureUrl() . '" />' . $imgs . '<br />';
            $row['itemno'] = '<span class="small">';
            $row['itemno'] .= $product->printCpf();
            $row['itemno'] .= '</span>';

            $row['category'] = $product->getLocalizedProductCategories('<br>');
            $row['creationDate'] = $product->creationDate;
            $response ['data'][] = $row;
        }

        return json_encode($response);
    }
}