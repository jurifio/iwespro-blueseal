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
        $datatable = new CDataTables('vBluesealProductNameList', ['id'], $_GET);

        $productNames = $this->app->repoFactory->create('ProductName')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('ProductName')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('ProductName')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productNames as $val){
			try {
				$response['data'][$i]["DT_RowId"] = 'row__' . $val->name;
				$response['data'][$i]["DT_RowClass"] = 'colore';
				$response['data'][$i]['name'] = $val->name;
                $res = $this->app->dbAdapter->query(
                    "SELECT `p`.`id` as `productId`, `p`.`productVariantId` FROM ((ProductNameTranslation as `pn` JOIN Product as `p` ON `p`.`productVariantId` = `pn`.`productVariantId`) JOIN `ProductStatus` as `ps` ON `p`.`productStatusId` = `ps`.`id`) WHERE `langId` = 1 AND `pn`.`name` = ? AND `ps`.`code` in ('P')",
                    [$val->name])->fetchAll();
                $response['data'][$i]['count'] = count($res); //$products->count();

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
                $response['data'][$i]['slug'] = implode('', $cats);

				$i++;
			} catch (\Exception $e) {
				throw $e;
			}
        }
        return json_encode($response);
    }
}