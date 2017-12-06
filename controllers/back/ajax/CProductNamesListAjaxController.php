<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CProductDetailListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CProductNamesListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "select `pn`.`id` AS `id`,`pnt`.`productId` AS `productId`,`pnt`.`productVariantId` AS `productVariantId`,`pn`.`name` AS `name`,`pn`.`langId` AS `langId`,`pc`.`id` AS `category`,0 AS `count` from (((((`ProductName` `pn` join `ProductNameTranslation` `pnt` on(((`pn`.`name` = `pnt`.`name`) and (`pn`.`langId` = `pnt`.`langId`)))) join `Product` `p` on(((`pnt`.`productId` = `p`.`id`) and (`pnt`.`productVariantId` = `p`.`productVariantId`)))) join `ProductSku` `ps` on(((`ps`.`productId` = `p`.`id`) and (`ps`.`productVariantId` = `p`.`productVariantId`)))) join (`ProductHasProductCategory` `phpc` join `ProductCategory` `pc` on((`phpc`.`productCategoryId` = `pc`.`id`))) on(((`p`.`id` = `phpc`.`productId`) and (`p`.`productVariantId` = `phpc`.`productVariantId`)))) join `ProductStatus` on((`ProductStatus`.`id` = `p`.`productStatusId`))) where ((`pn`.`langId` = 1) and (`pnt`.`langId` = 1) and (`ps`.`stockQty` > 0) and (not((`p`.`dummyPicture` like '%bs-dummy%'))) and (`p`.`productStatusId` in (5,6,11)))";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $mark = \Monkey::app()->router->request()->getRequestData('marks');
        if ('con' === $mark) {
            $datatable->addIgnobleCondition('name', '% !', false);
        } elseif ('senza' === $mark) {
            $datatable->addIgnobleCondition('name', '% !', true);
        }

        $productNames = \Monkey::app()->repoFactory->create('ProductName')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('ProductName')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ProductName')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach($productNames as $val){
			try {
                $row = [];
				$row["DT_RowId"] = 'row__' . $val->id;
				$row["DT_RowClass"] = 'colore';
				$row['name'] = $val->name;
                $res = \Monkey::app()->dbAdapter->query(
                    "SELECT `p`.`id` as `productId`, `p`.`productVariantId` FROM ((ProductNameTranslation as `pn` JOIN Product as `p` ON `p`.`productVariantId` = `pn`.`productVariantId`) JOIN `ProductStatus` as `ps` ON `p`.`productStatusId` = `ps`.`id`) WHERE `langId` = 1 AND `pn`.`name` = ? AND `ps`.`code` in ('A', 'P', 'I') AND (`p`.`qty` > 0) AND (`p`.`dummyPicture` NOT LIKE '%bs-dummy%')",
                    str_replace(' !', '', [$val->name]))->fetchAll();
                $row['count'] = count($res); //$products->count();

                $iterator = 0;
                $cats = [];
                foreach($res as $v) {
                    if (10 == $iterator) break;
                    $p = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $v['productId'], 'productVariantId' => $v['productVariantId']]);
                    foreach($p->productCategoryTranslation as $cat) {
                        $path = $this->app->categoryManager->categories()->getPath($cat->productCategoryId);
                        unset($path[0]);
                        $newCat = '<span class="small">'.implode('/',array_column($path, 'slug')).'</span><br />';
                        if (in_array($newCat, $cats)) continue;
                        $cats[] = $newCat;
                        $iterator++;
                        if (10 == $iterator) break;
                    }
                }
                $row['category'] = implode('', $cats);
                $response ['data'][] = $row;
			} catch (\Throwable $e) {
				throw $e;
			}
        }
        return json_encode($response);
    }
}