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
class CProductDetailListAjaxController extends AAjaxController
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
        $datatable = new CDataTables('vBluesealProductDetailList',['id'],$_GET);
        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $modifica = $this->urls['base']."prodotti/modifica";

        $productsDetail = $this->app->repoFactory->create('ProductDetail')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsDetail->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsDetail->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productsDetail as $val){

            \BlueSeal::dump($val);


            $query = $this->app->dbAdapter->query("SELECT count(psa.productVariantId) AS conto
                                                  FROM ProductDetail pd, ProductSheetActual psa
                                                  WHERE pd.id = psa.productDetailId and pd.id = ?",[$val->id])->fetchAll()[0];

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = $val->productDetailTranslation->findOneByKey('langId',1)->name;
            $response['data'][$i]['slug'] = $val->slug;

            $pId = $this->app->dbAdapter->query(
                "SELECT 
                  psa.productId as productId,
                  psa.productVariantId as productVariantId 
                  FROM ProductSheetActual psa, ProductDetail pd  
                  WHERE pd.id = psa.productDetailId AND psa.productDetailId = ? ",
                [$val->id ]
                        )->fetchAll();
            $productList = '<div class="visualizzaButton" data-lineCollapse="' . $response['data'][$i]['slug'] .'" style="width: 250px">Visualizza &darr;</div><div style="display: hidden" id="dettCollaps-' . $response['data'][$i]['slug'] . '" class="detCollapsed">';

            foreach($pId as $v) {
                $cats = $this->app->categoryManager->getCategoriesForProduct($v['productId'], $v['productVariantId']);
                $catsName = [];
                foreach($cats as $catv) {
                    $catName = $this->app->dbAdapter->select('ProductCategoryTranslation', ['productCategoryId' => $catv['id'], 'langId' => 1])->fetchAll();
                    \BlueSeal::dump($catName);
                    if (isset($catName[0])) $catsName[] = $catName[0]['name'];
                }
                $cats = implode( " - " , $catsName);
                $productList .= '<a style="width: 120px" href="' . $modifica . "?id=" . $v['productId'] . '&productVariantId=' . $v['productVariantId'] . '">' . $v['productId'] . '-' . $v['productVariantId'] . '</a><span style="width: 250px;"> cat: '. $cats  .'</span><br />';
            }
            $productList .= '</div>';
            $response['data'][$i]['products'] = $productList;
            $response['data'][$i]['num'] = $query['conto'];
            $i++;
        }

        return json_encode($response);
    }
}