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
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductPickyController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_picky";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_picky.php');

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
        $qry = "SELECT COUNT(DISTINCT dp.id) AS conto
                FROM DirtyProduct dp, DirtySku ds
                WHERE dp.id = ds.dirtyProductId AND
                ds.shopId = ? AND
                dp.productVariantId IS NULL AND
                dp.dirtyStatus in ('E', '', 'F')";

        $repo = $this->app->repoFactory->create('Shop');
        foreach ($shops as $key => $shop) {
            $one = $repo->findOne([$shop]);
            if (isset($one->importer)) {
                $one->roulette = $this->app->dbAdapter->query($qry, [$shop])->fetchAll()[0]['conto'];
                $roulette[$one->name] = $one->title." (".$one->roulette.")";
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
            'roulette' => $roulette,
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