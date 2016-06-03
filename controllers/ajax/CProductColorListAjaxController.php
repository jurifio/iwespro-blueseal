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
class CProductColorListAjaxController extends AAjaxController
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
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {
        $datatable = new CDataTables('vBluesealProductColorList',['id', 'productVariantId'],$_GET);
        
        $products = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($products as $v){
			try {
				$response['data'][$i]["DT_RowId"] = 'row__' . $v->id;
				$response['data'][$i]["DT_RowClass"] = 'colore';
                $response['data'][$i]['code'] = '<a href="/blueseal/prodotti/modifica?id=' . $v->id . '&productVariantId=' .$v->productVariantId .'">' . $v->id . '-' . $v->productVariantId . '</a>';
				$response['data'][$i]['colorName'] = $v->productVariant->name;
                $colorGroupCollection = $v->productColorGroup->findOneByKey("langId", 1);
				$response['data'][$i]['colorGroupName'] = ($colorGroupCollection) ? $colorGroupCollection->name : '-';
                $dummyPic = ($v->dummyPicture) ? $v->dummyPicture : "/assets/bs-dummy-16-9.png";
                $response['data'][$i]['dummyPic'] = '<img width="80" src="' . $dummyPic . '">';
                $response['data'][$i]['categorie'] = '';

                foreach($v->productCategory as $cat) {
                    $res = $this->app->dbAdapter->query("SELECT replace(group_concat(name), ',', '/') AS name FROM `ProductCategory` AS `pc` JOIN `ProductCategoryTranslation` AS `pct` WHERE `pc`.`id` = `pct`.productCategoryId AND `pc`.`lft` <= ? AND `pc`.`rght` >= ? AND pct.langId = 1 ORDER BY `pc`.`lft` ASC"
                        , [$cat->lft, $cat->rght])->fetchAll();

                    $response['data'][$i]['categorie'] .= '<span style="font-size: 0.8em">' . $res[0]['name'] . '</span><br />';
                }
                $res = $this->app->repoFactory->create("DirtyProduct")->em()->findOneBy(['productId' => $v->id, 'productVariantId' => $v->productVariantId]);
                $response['data'][$i]['var'] = $res->var;
                $i++;
			} catch (\Exception $e) {
				throw $e;
			}
        }
        return json_encode($response);
    }
}