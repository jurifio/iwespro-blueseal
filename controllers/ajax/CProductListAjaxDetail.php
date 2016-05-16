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
class CProductListAjaxDetail extends AAjaxController
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

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {

        $modifica = $this->urls['base']."prodotti/modifica";
        
        $idDetail = $this->app->router->request()->getRequestData();
        foreach ($idDetail as $k => $idD) {
            $idDetail[$k] = "psa.productDetailId = " . $idD;
        }
        $where = implode(" AND ", $idDetail);

        $pId = $this->app->dbAdapter->query(
            "SELECT 
                  psa.productId as productId,
                  psa.productVariantId as productVariantId 
                  FROM ProductSheetActual psa, ProductDetail pd  
                  WHERE pd.id = psa.productDetailId AND " . $where,
            []
        )->fetchAll();
        $productList = "";
        foreach($pId as $v) {
            $cats = $this->app->categoryManager->getCategoriesForProduct($v['productId'], $v['productVariantId']);
            $catsName = [];
            foreach($cats as $catv) {
                $catName = $this->app->dbAdapter->select('ProductCategoryTranslation', ['productCategoryId' => $catv['id'], 'langId' => 1])->fetchAll();
                if (isset($catName[0])) $catsName[] = $catName[0]['name'];
            }
            $cats = implode( " - " , $catsName);
            
            $productList .= '<a style="width: 120px" href="' . $modifica . "?id=" . $v['productId'] . '&productVariantId=' . $v['productVariantId'] . '">' . $v['productId'] . '-' . $v['productVariantId'] . '</a><span style="width: 250px;"> cat: '. $cats  .'</span><br />';
        }

        return $productList;
    }
}