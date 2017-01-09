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
class CFriendsListAjaxController extends AAjaxController
{
    public function get()
    {
        $shopIds = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();

        $sql = "SELECT
  `s`.`name`                                                                              AS `shop`,
  concat(`pse`.`name`, ' ', `pse`.`year`)                                                 AS `season`,
  count(DISTINCT `p`.`id`, `p`.`productVariantId`)                                        AS `prodotti`,
  count(DISTINCT if((`p`.`productStatusId` = 6), `p`.`productVariantId`, NULL))           AS `pubblicati`,
  sum(`ps`.`stockQty`)                                                                    AS `quantita`,
  round(sum(`ps`.`value`), 0)                                                             AS `valore_al_costo`,
  round(sum(`ps`.`price`), 0)                                                             AS `valore_al_prezzo`,
  round(
      ifnull(sum(if(((`ol`.`id` IS NOT NULL) AND (`ols`.`id` IN (7, 8, 19, 10, 11, 12, 13))), `ol`.`friendRevenue`, 0)),
             0), 0)                                                                       AS `incasso_friend`,
  round(ifnull(sum(if(((`ol`.`id` IS NOT NULL) AND (`ols`.`id` IN (7, 8, 19, 10, 11, 12, 13))),
                      (`ol`.`netPrice` - `ol`.`friendRevenue`), 0)), 0), 0)               AS `incasso_picky`,
  sum(if(((`ol`.`id` IS NOT NULL) AND (`ols`.`id` IN (7, 8, 19, 10, 11, 12, 13))), 1, 0)) AS `venduto`,
  sum(if(((`ol`.`id` IS NOT NULL) AND (`ols`.`id` IN (6, 14, 16, 15))), 1, 0))            AS `cancellato`
FROM (((((((`Product` `p`
  JOIN `ProductSeason` `pse`) JOIN `ProductSku` `ps`) JOIN `ShopHasProduct` `shp`) JOIN `Shop` `s`) LEFT JOIN
  `OrderLine` `ol` ON ((
    (`ol`.`productId` = `ps`.`productId`) AND (`ol`.`productVariantId` = `ps`.`productVariantId`) AND
    (`ol`.`productSizeId` = `ps`.`productSizeId`) AND (`ol`.`shopId` = `ps`.`shopId`)))) LEFT JOIN
  `OrderLineStatus` `ols` ON ((`ol`.`status` = convert(`ols`.`code` USING utf8)))) LEFT JOIN `Order` `o`
    ON ((`o`.`id` = `ol`.`orderId`)))
WHERE ((`p`.`productSeasonId` = `pse`.`id`) AND (`p`.`id` = `shp`.`productId`) AND
       (`p`.`productVariantId` = `shp`.`productVariantId`) AND (`shp`.`shopId` = `s`.`id`) AND
       (`shp`.`productId` = `ps`.`productId`) AND (`shp`.`productVariantId` = `ps`.`productVariantId`) AND
       (`shp`.`shopId` = `ps`.`shopId`))
GROUP BY `s`.`id`, `pse`.`id`";
        $datatable = new CDataTables($sql,['id'],$_GET,true);
        $datatable->addSearchColumn('shop',$shopIds);

        $orribilità = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        $count = $this->app->dbAdapter->query($datatable->getQuery(true,true),$datatable->getParams())->fetch();
        $totalCount = $this->app->dbAdapter->query($datatable->getQuery('full',true),$datatable->getParams())->fetch();

        /*$brands = $this->app->repoFactory->create('ProductBrand')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('ProductBrand')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('ProductBrand')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        */
        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = $orribilità;

        foreach($response['data'] as $k => $v) {
            if ((0 == $v['pubblicati']) || (0 == $v['pubblicati'])) $percentPublished = '-';
            else $percentPublished  = round($v['pubblicati'] / $v['prodotti'] * 100, 2) . '%';
            $response['data'][$k]['pubblicati'] .= ' <span class="small">(' . $percentPublished . ')</span>';
        }

        return json_encode($response);
    }
}