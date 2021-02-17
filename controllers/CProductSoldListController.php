<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CProductFastListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/05/2020
 * @since 1.0
 */
class CProductSoldListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_sold_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_sold_list.php');
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
        if(isset($_GET['dateStart'])){
            $dateStart=$_GET['dateStart'];
        }else{
            $dateStart= (new \DateTime())->modify("-1 day")->format('Y-m-d H:i:s');
        }

        /** LOGICA */
        $bluesealBase = $this->app->baseUrl(false) . '/blueseal/';
        $pageURL = $bluesealBase . "prodotti";
        $aggiungi = $bluesealBase . "prodotti/aggiungi";
        $carica = $bluesealBase . "skus";
        $foto = $bluesealBase . "carica_foto.php";
        $dummyUrl = $this->app->cfg()->fetch('paths', 'dummyUrl');
        $productBrand=\Monkey::app()->repoFactory->create('ProductBrand')->findAll();
        $Shop=\Monkey::app()->repoFactory->create('Shop')->findAll();

        $shops = [];
        if ($this->app->getUser()->hasPermission('allShops')) {

        } else {
            $res = $this->app->dbAdapter->select('UserHasShop', ['userId' => $this->app->getUser()->getId()])->fetchAll();
            foreach ($res as $val) {
                $shops[] = $val['shopId'];
            }
        }
        $prodotti = null;

        if (count($shops) == 0) {
            $res = $this->app->dbAdapter->select('Shop')->fetchAll();
            foreach ($res as $val) {
                $shops[] = $val['id'];
            }
        }

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        foreach($productStatuses as $status){
            $statuses[$status->code] = $status->name;
        }

        //img count
        $imgs = 0;
        foreach ($this->app->theme->getImageSizes() as $v) {
            $imgs++;
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'dummyUrl' => $dummyUrl,
            'statuses' => $statuses,
            'aggiungi' => $aggiungi,
            'carica' => $carica,
            'foto' => $foto,
            'imgs' => $imgs,
            'bluesealBase' => $bluesealBase,
            'cm' => $this->app->categoryManager,
            'pageURL' => $pageURL,
            'prodotti' => $prodotti,
            'season'=>$season,
            'productZeroQuantity'=>$productZeroQuantity,
            'productStatus'=>$productStatus,
            'page' => $this->page,
            'productBrand'=>$productBrand,
            'productBrandId'=>$productBrandId,
            'Shop'=>$Shop,
            'shopid'=>$shopid,
            'stored'=>$stored,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {

    }
}