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
        $this->em->productsDetail = $this->app->entityManagerFactory->create('ProductDetail');

        return $this->{$action}();
    }

    public function get()
    {
        $datatable = new CDataTables('vBluesealProductSales', ['id', 'productVariantId', 'name'], $_GET);
        $datatable->addGroup(['name']);

        $productNames = $this->app->repoFactory->create('ProductNameTranslation')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsDetail->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsDetail->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $modifica = $this->urls['base'] . "prodotti/modifica";
        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productNames as $val){
			try {
				$response['data'][$i]["DT_RowId"] = 'row__' . $val->productId . '__' . $val->productVariantId;
				$response['data'][$i]["DT_RowClass"] = 'colore';
				$response['data'][$i]['name'] = $val->name;
                $res = $this->app->dbAdapter->query("SELECT * FROM ProductNameTranslation WHERE `langId` = 1 AND `name` = ?",[$val->name])->fetchAll();
                $response['data'][$i]['count'] = count($res); //$products->count();
                $response['data'][$i]['productsList'] = '';
                $response['data'][$i]['productsList'] .= '<span class="small">';
                $iterator = 0;
                foreach($res as $v) {
                    $p = $this->app->repoFactory->create('Product')->findOneBy(['id' => $v['productId'], 'productVariantId' => $v['productVariantId']]);
                    $cats = [];
                    foreach($p->productCategoryTranslation as $cat){

                        $path = $this->app->categoryManager->categories()->getPath($cat->productCategoryId);
                        unset($path[0]);
                        $newCat = '<span class="small">'.implode('/',array_column($path, 'slug')).'</span><br />';
                        if (array_search($newCat, $cats)) continue;
                        $cats[] = $newCat;
                        $iterator++;
                        if (10 == $iterator) break;
                    }
                    if (10 == $iterator) break;
                }
                $response['data'][$i]['slug'] = implode('', $cats);
                $iterator = 0;
                foreach($res as $v) {
                    $prod = $this->app->repoFactory->create('Product')->findOneBy(['id' => $v['productId'], 'productVariantId' => $v['productVariantId']]);
                    $response['data'][$i]['productsList'] .=
                        $okManage ?
                            '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id='.$prod->id.'&productVariantId='.$prod->productVariantId.'">'.$prod->id.'-'.$prod->productVariantId.'</a>'
                            : $val->id.'-'.$val->productVariantId;
                    $response['data'][$i]['productsList'] .= ' - CPF: ' . $prod->itemno;
                    $response['data'][$i]['productsList'] .= ' - brand: ' . $prod->productBrand->name;
                    $img = strpos($prod->dummyPicture,'s3-eu-west-1.amazonaws.com') ? $prod->dummyPicture : $this->urls['dummy']."/".$prod->dummyPicture;
                    $response['data'][$i]['productsList'] .= ' <img width="30" src="' . $img . '" /><br />';
                    $iterator++;
                    if (10 == $iterator) break;
                }
                if (10 < $response['data'][$i]['count']) $response['data'][$i]['productsList'] .= "...";
                $response['data'][$i]['productsList'] .= '</span>';
				$i++;
			} catch (\Exception $e) {
				throw $e;
			}

        }
        return json_encode($response);
    }

    
}