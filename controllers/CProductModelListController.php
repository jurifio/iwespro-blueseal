<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CProductListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductModelListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_model_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_model_list.php');

        /** LOGICA */
        $bluesealBase = $this->app->baseUrl(false) . '/blueseal/';
        $pageURL = $bluesealBase . "prodotti";
        $aggiungi = $bluesealBase . "prodotti/aggiungi";
        $carica = $bluesealBase . "skus";
        $foto = $bluesealBase . "carica_foto.php";
        $dummyUrl = $this->app->cfg()->fetch('paths', 'dummyUrl');

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
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {

    }
}