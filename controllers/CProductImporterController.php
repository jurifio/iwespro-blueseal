<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CProductImporterController
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
class CProductImporterController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_importer_bug_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/product_incomplete_import.php');

        /** LOGICA */
        $bluesealBase = $this->app->baseUrl(false).'/blueseal/';
        $pageURL = $bluesealBase."prodotti";
        $modifica = $bluesealBase."prodotti/aggiungi";
        $carica = $bluesealBase."skus";
        $foto = $bluesealBase."carica_foto.php";
        $importa_foto_cartechini = $bluesealBase."import/cartechiniphoto/total_import_cartechini.php";
        $importa_foto_newage = $bluesealBase."import/cartechiniphoto/total_import_newage.php";
        $dummyUrl = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasRole('ownerEmployee')) {

        } else if($this->app->getUser()->hasRole('friendEmployee')) {
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            $shops = [];
            foreach($res as $val){
                $shops[] = $val['shopId'];
            }
        }

        $prodotti = null;

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        foreach($productStatuses as $status){
            $statuses[$status->code] = $status->name;
        }

        //img count
        $imgs = 0;
        foreach($this->app->theme->getImageSizes() as $v){
            $imgs++;
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'dummyUrl' =>$dummyUrl,
            'statuses' =>$statuses,
            'modifica' =>$modifica,
            'carica' =>$carica,
            'foto' =>$foto,
            'imgs'=>$imgs,
            'bluesealBase' =>$bluesealBase,
            'cm'=>$this->app->categoryManager,
            'pageURL'=>$pageURL,
            'prodotti'=>$prodotti,
            'importa_foto_cartechini' => $importa_foto_cartechini,
            'importa_foto_newage' => $importa_foto_newage,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {

    }
}