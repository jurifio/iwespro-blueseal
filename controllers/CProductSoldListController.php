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
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/product_sold_list.php');
        if (isset($_GET['season'])) {
            $season = $_GET['season'];
        } else {
            $season = 0;
        }
        if (isset($_GET['productZeroQuantity'])) {
            $productZeroQuantity = $_GET['productZeroQuantity'];
        } else {
            $productZeroQuantity = 0;
        }
        if (isset($_GET['productStatus'])) {
            $productStatus = $_GET['productStatus'];
        } else {
            $productStatus = 0;
        }
        if (isset($_GET['productBrandId'])) {
            $productBrandId = $_GET['productBrandId'];
        } else {
            $productBrandId = 0;
        }
        if (isset($_GET['shopid'])) {
            $shopid = $_GET['shopid'];
        } else {
            $shopid = 0;
        }
        if (isset($_GET['stored'])) {
            $stored = $_GET['stored'];
        } else {
            $stored = 0;
        }
        if (isset($_GET['dateStart'])) {
            $dateStart = $_GET['dateStart'];
            $timeStartMask = (new \DateTime($_GET['dateStart']))->format('Y-m-d H:i:s');
        } else {
            $dateStart = (new \DateTime())->modify("midnight")->format('Y-m-d\TH:i:s');
            $timeStartMask = (new \DateTime())->modify("midnight")->format('Y-m-d H:i:s');
        }
        if (isset($_GET['dateEnd'])) {
            $dateEnd = $_GET['dateEnd'];
            $timeEndMasks = (new \DateTime($_GET['dateEnd']))->format('Y-m-d H:i:s');
        } else {
            $dateEnd = (new \DateTime())->modify("tomorrow midnight")->format('Y-m-d\TH:i:s');
            $timeEndMasks = (new \DateTime())->modify("tomorrow midnight")->format('Y-m-d H:i:s');
        }


        if ($shopid != 0) {
            $sqlShopFilter = ' psd.shopId=' . $shopid.' and ';
        } else {
            $sqlShopFilter = '';
        }
        $titleShop = "Grafico Shop";
        $sqlShop = "SELECT 
		 psd.shopId AS shopId, 
		 SUM(psd.soldQuantity) AS qty, 
		 SUM(psd.netTotal) AS netTotal,
		 s.name AS shopName
		 FROM ProductSoldDay psd JOIN 
   Product p ON p.id=psd.productId AND p.productVariantId=psd.productVariantId 
JOIN ShopHasProduct shp ON psd.productId=shp.productId AND psd.productVariantId=shp.productVariantId 
JOIN ProductBrand pb ON p.productBrandId=pb.id
JOIN Shop s ON psd.shopId=s.id WHERE 1=1 and ".$sqlShopFilter."   psd.soldQuantity>0 and psd.dateStart>='" . $timeStartMask . "' and psd.dateEnd<='" . $timeEndMasks . "' 
    GROUP by s.name  ORDER BY sum(psd.netTotal) desc";
        $titleBrand = "Grafico Top 10 brand";

        $sqlBrand = "SELECT 
		 SUM(psd.soldQuantity) AS qty, 
		 SUM(psd.netTotal) AS netTotal,
		 pb.name AS productBrandName,
		 date_format(psd.dateStart,'%d') AS dayStat
		 FROM ProductSoldDay psd JOIN  
   Product p ON p.id=psd.productId AND p.productVariantId=psd.productVariantId 
JOIN ShopHasProduct shp ON psd.productId=shp.productId AND psd.productVariantId=shp.productVariantId 
JOIN ProductBrand pb ON p.productBrandId=pb.id
JOIN Shop s ON psd.shopId=s.id WHERE 1=1 and  ".$sqlShopFilter."   psd.soldQuantity>0 and psd.dateStart>='" . $timeStartMask . "' and psd.dateEnd<='" . $timeEndMasks . "' 
    GROUP by pb.name  ORDER BY sum(psd.netTotal) desc limit 10";

        $stats = [];
        $arrayLabelShop = '';
        $arrayQtyShop = '';
        $arrayValueShop = '';
        $resShop = \Monkey::app()->dbAdapter->query($sqlShop,[])->fetchAll();
        if (count($resShop) > 0) {
            foreach ($resShop as $shopvalues) {
                $arrayLabelShop .= $shopvalues['shopName'] . ',';
                $arrayQtyShop .= $shopvalues['qty'] . ',';
                $arrayValueShop .= number_format($shopvalues['netTotal'],2,'.','') . ',';


            }
        } else {
            $arrayLabelShop = '';
            $arrayQtyShop = "0";
            $arrayValueShop = "0.00";
        }
        $arrayLabelBrand = '';
        $arrayQtyBrand = '';
        $arrayValueBrand = '';
        $resBrand = \Monkey::app()->dbAdapter->query($sqlBrand,[])->fetchAll();
        if (count($resBrand) > 0) {
            foreach ($resBrand as $brandvalues) {
                $arrayLabelBrand .= $brandvalues['productBrandName'] . ',';
                $arrayQtyBrand .= $brandvalues['qty'] . ',';
                $arrayValueBrand .= number_format($brandvalues['netTotal'],2,'.','') . ',';


            }
        } else {
            $arrayLabelBrand = '';
            $arrayQtyBrand = "0";
            $arrayValueBrand = "0.00";
        }


        /** LOGICA */
        $bluesealBase = $this->app->baseUrl(false) . '/blueseal/';
        $pageURL = $bluesealBase . "prodotti";
        $aggiungi = $bluesealBase . "prodotti/aggiungi";
        $carica = $bluesealBase . "skus";
        $foto = $bluesealBase . "carica_foto.php";
        $dummyUrl = $this->app->cfg()->fetch('paths','dummyUrl');
        $productBrand = \Monkey::app()->repoFactory->create('ProductBrand')->findAll();
        $Shop = \Monkey::app()->repoFactory->create('Shop')->findAll();

        $shops = [];
        if ($this->app->getUser()->hasPermission('allShops')) {

        } else {
            $res = $this->app->dbAdapter->select('UserHasShop',['userId' => $this->app->getUser()->getId()])->fetchAll();
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
        foreach ($productStatuses as $status) {
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
            'season' => $season,
            'productZeroQuantity' => $productZeroQuantity,
            'productStatus' => $productStatus,
            'page' => $this->page,
            'productBrand' => $productBrand,
            'productBrandId' => $productBrandId,
            'Shop' => $Shop,
            'shopid' => $shopid,
            'stored' => $stored,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'arrayLabelBrand' => substr($arrayLabelBrand,0,-1),
            'arrayQtyBrand' => substr($arrayQtyBrand,0,-1),
            'arrayValueBrand' => substr($arrayValueBrand,0,-1),
            'arrayLabelShop' => substr($arrayLabelShop,0,-1),
            'arrayQtyShop' => substr($arrayQtyShop,0,-1),
            'arrayValueShop' => substr($arrayValueShop,0,-1),
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {

    }
}