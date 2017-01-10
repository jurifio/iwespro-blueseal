<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CNameTranslateListAjaxController
 * @package redpanda\blueseal\controllers\ajax
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
class CNameTranslateListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        $this->em = new \stdClass();
        $this->em->productsName = $this->app->entityManagerFactory->create('ProductNameTranslation');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "select `pn`.`id` AS `id`,`pnt`.`productId` AS `productId`,`pnt`.`productVariantId` AS `productVariantId`,`pn`.`name` AS `name`,`pn`.`langId` AS `langId`,`pc`.`id` AS `category`,0 AS `count` from (((((`ProductName` `pn` join `ProductNameTranslation` `pnt` on(((`pn`.`name` = `pnt`.`name`) and (`pn`.`langId` = `pnt`.`langId`)))) join `Product` `p` on(((`pnt`.`productId` = `p`.`id`) and (`pnt`.`productVariantId` = `p`.`productVariantId`)))) join `ProductSku` `ps` on(((`ps`.`productId` = `p`.`id`) and (`ps`.`productVariantId` = `p`.`productVariantId`)))) join (`ProductHasProductCategory` `phpc` join `ProductCategory` `pc` on((`phpc`.`productCategoryId` = `pc`.`id`))) on(((`p`.`id` = `phpc`.`productId`) and (`p`.`productVariantId` = `phpc`.`productVariantId`)))) join `ProductStatus` on((`ProductStatus`.`id` = `p`.`productStatusId`))) where ((`pn`.`langId` = 1) and (`pnt`.`langId` = 1) and (`ps`.`stockQty` > 0) and (not((`p`.`dummyPicture` like '%bs-dummy%'))) and (`p`.`productStatusId` in (5,6,11)))";
        $datatable = new CDataTables($sql,['id'],$_GET,true);
        $modifica = $this->urls['base']."traduzioni/nomi/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }
        $datatable->addCondition('langId',[1]);
        $datatable->addCondition('name',[''],true);

        $productName = $this->app->repoFactory->create('ProductName')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsName->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsName->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $PNRepo = $this->app->repoFactory->create('ProductName');
        $repo = $this->app->repoFactory->create('Lang');
        $installedLang = $repo->findAll();

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productName as $val){
            $html = '';

            foreach ($installedLang as $insLang) {
                $translated = $PNRepo->findOneBy(['name' => $val->name, 'langId' => $insLang->id]);
                if(!is_null($translated) && ($translated->name != '')) {
                    $html .= '<span class="badge">' . $insLang->lang . '</span>';
                } else {
                    $html .= '<span class="badge badge-red">' . $insLang->lang . '</span>';
                }
            }

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            //$response['data'][$i]['name'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'. $modifica . '?name=' . urlencode($val->name) . '">' . $val->name . '</a>' : $val->name;
            $response['data'][$i]['name'] = $val->name;
            $response['data'][$i]['lang'] = $html;

            $res = \Monkey::app()->dbAdapter->query(
                "SELECT `p`.`id` as `productId`, `p`.`productVariantId` FROM ((ProductNameTranslation as `pn` JOIN Product as `p` ON `p`.`productVariantId` = `pn`.`productVariantId`) JOIN `ProductStatus` as `ps` ON `p`.`productStatusId` = `ps`.`id`) WHERE `langId` = 1 AND `pn`.`name` = ? AND `ps`.`code` in ('A', 'P', 'I') AND (`p`.`qty` > 0) AND (`p`.`dummyPicture` NOT LIKE '%bs-dummy%')",
                str_replace(' !', '', [$val->name]))->fetchAll();
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

            $response['data'][$i]['category'] = implode('', $cats);

            $i++;
        }

        return json_encode($response);
    }
}