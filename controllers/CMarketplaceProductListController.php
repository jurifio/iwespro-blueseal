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
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CMarketplaceProductListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/marketplace_product_list.php');

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

        $roulette = [];


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'dummyUrl' => $dummyUrl,
            'roulette' => $roulette,
            'aggiungi' => $aggiungi,
            'carica' => $carica,
            'foto' => $foto,
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