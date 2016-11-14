<?php
namespace bamboo\blueseal\controllers\ajax;

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
        $datatable = new CDataTables('vBluesealProductNameNewList', ['id'], $_GET);
        $productNames = $this->app->repoFactory->create('ProductName')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('ProductName')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('ProductName')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

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
                    $p = $this->app->repoFactory->create('Product')->findOneBy(['id' => $v['productId'], 'productVariantId' => $v['productVariantId']]);
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