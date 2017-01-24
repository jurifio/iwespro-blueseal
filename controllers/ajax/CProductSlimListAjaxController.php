<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;

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
class CProductSlimListAjaxController extends AAjaxController
{
    public function get()
    {
        $shopsIds = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();

        $user = \Monkey::app()->getUser();
        $allShops = $user->hasPermission('allShops');

        $sql = "SELECT
                  `p`.`id`                                             AS `id`,
                  `p`.`productVariantId`                               AS `productVariantId`,
                  concat(`p`.`id`, '-', `p`.`productVariantId`)        AS `code`,
                  `pb`.`name`                                          AS `brand`,
                  concat(`p`.`itemno`, ' # ', `pv`.`name`)             AS `cpf`,
                  `shp`.`extId`                                        AS `externalId`,
                  concat(`ps`.`name`, ' ', `ps`.`year`)                AS `season`,
                  `s`.`id`                                             AS `shopId`,
                  `s`.`title`                                          AS `shop`,
                  if((`p`.`qty` > 0), 'disponibile', 'mancante')       AS `stock`,
                  `p`.`creationDate`                                   AS `creationDate`,
                  `pss`.`name`                                         AS `status`,
                  if(exists (select * from ProductSheetActual psa where psa.productId = p.id and psa.productVariantId = p.productVariantId), 'si', 'no') AS `details`
                FROM (((((((`Product` `p`
                  JOIN `ProductVariant` `pv` on `p`.`productVariantId` = `pv`.`id`)
                  JOIN `ProductBrand` `pb` on `p`.`productBrandId` = `pb`.`id`)
                  JOIN `ProductStatus` `pss` on `pss`.`id` = `p`.`productStatusId`)
                  JOIN `ShopHasProduct` `shp` ON `p`.`id` = `shp`.`productId` AND `p`.`productVariantId` = `shp`.`productVariantId`)
                  JOIN `Shop` `s` on `s`.`id` = `shp`.`shopId`)
                  JOIN `ProductSeason` `ps` ON ((`p`.`productSeasonId` = `ps`.`id`))))
                WHERE `pss`.`id` NOT IN (7, 8)
                GROUP BY `p`.`id`, `p`.`productVariantId`, `s`.`id`
                ORDER BY `p`.`creationDate` DESC";
        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET,true);
        $datatable->addCondition('shopId', $shopsIds);
        if (!$allShops) $datatable->addLikeCondition('status', 'Fuso', true);

        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Product')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $okManage = $user->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        $modifica = '/blueseal/friend/prodotti/modifica';

        foreach ($prodotti as $val) {
            $row = [];
            /** @var CProduct $val */
            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';
            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $val->id . '&productVariantId=' . $val->productVariantId . '">' . $val->id . '-' . $val->productVariantId . '</a>' : $val->id . '-' . $val->productVariantId;

            if ($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";
            $row['image'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $val->getDummyPictureUrl() . '" /></a>' . $imgs . '<br />';

            $row['shop'] = '<span>';
            $row['shop'] .= $val->getShops('<br />');
            $row['shop'] .= '</span>';

            $row['details'] = $val->productSheetActual->count() ? 'Sì' : 'No';

            $row['externalId'] = '<span class="small">';
            $row['externalId'] .= $val->getShopExtenalIds('<br />');
            $row['externalId'] .= '</span>';

            $row['cpf'] = '<span class="small">';
            $row['cpf'] .= $val->itemno . ' # ' . $val->productVariant->name;
            $row['cpf'] .= '</span>';

            $row['brand'] = $val->productBrand->name;

            $row['categories'] = '<span class="small">';
            $row['categories'] .= $val->getLocalizedProductCategories('<br />');
            $row['categories'] .= '</span>';

            $row['stock'] = '<table class="nested-table inner-size-table" data-product-id="'.$val->printId().'"></table>';

            $row['season'] = '<span class="small">';
            $row['season'] .= ($val->productSeason) ? $val->productSeason->name . " " . $val->productSeason->year : '-';
            $row['season'] .= '</span>';

            if ($allShops) $status = $val->productStatus->name;
            else {
                $friendQty = 0;
                foreach($val->productSku as $sku) {
                    foreach($shopsIds as $sid) {
                        if ($sku->shopId == $sid) {
                            $friendQty = $friendQty + $sku->stockQty;
                        }
                    }
                }
                if ($friendQty) $status = $val->productStatus->name;
                else $status = 'Esaurito';
            }

            $row['status'] = $status;
            $row['creationDate'] = $val->creationDate;
            $response['data'][] = $row;
        }
        return json_encode($response);
    }
}