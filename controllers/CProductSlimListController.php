<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CProductSlimListController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/09/2016
 * @since 1.0
 */
class CProductSlimListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_slim_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_slim_list.php');
        if(isset($_GET['season'])) {
            $season=$_GET['season'];
        }else{
            $season=0;
        }
        if(isset($_GET['productZeroQuantity'])) {
            $productZeroQuantity=$_GET['productZeroQuantity'];
        }else{
            $productZeroQuantity=0;
        }
        if(isset($_GET['productStatus'])) {
            $productStatus=$_GET['productStatus'];
        }else{
            $productStatus=0;
        }
        if(isset($_GET['productBrandId'])){
            $productBrandId=$_GET['productBrandId'];
        } else{
            $productBrandId=0;
        }
        if(isset($_GET['shopid'])){
            $shopid=$_GET['shopid'];
        } else{
            $shopid=0;
        }
        if(isset($_GET['stored'])){
            $stored=$_GET['stored'];
        } else{
            $stored=0;
        }

        $shops = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $productBrand=\Monkey::app()->repoFactory->create('ProductBrand')->findAll();
        $Shop=\Monkey::app()->repoFactory->create('Shop')->findAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'shops' => $shops,
            'season'=>$season,
            'productZeroQuantity'=>$productZeroQuantity,
            'productStatus'=>$productStatus,
            'productBrand'=>$productBrand,
            'productBrandId'=>$productBrandId,
            'Shop'=>$Shop,
            'shopid'=>$shopid,
            'stored'=>$stored,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}