<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;


class CGainPlanPassiveMovementListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "gainplanpassivemovement_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/gainplanpassivemovement_list.php');

        $permission = \Monkey::app()->getUser()->hasPermission('allShops');

        /** LOGICA */
        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $pageURL = $blueseal."/registri/gainplan-passivo";

        $opera = $blueseal."/registri/gainplan-passivo";
        $aggiungi = $blueseal."/registri/gainplan-passivo";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'pageURL' =>$pageURL,
            'operaURL' =>$opera,
            'aggiungiURL' =>$aggiungi,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'permission' => $permission
        ]);
    }
}