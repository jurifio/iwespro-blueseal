<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;


class CGainPlanListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "gainplan_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/gainplan_list.php');

        $permission = \Monkey::app()->getUser()->hasPermission('allShops');
        $currentYear= date('Y');
        $valueRequest = \Monkey::app()->router->request()->getRequestData('countYear');
        if($valueRequest!=false){
            $currentYear=$valueRequest;
        }

        /** LOGICA */
        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $pageURL = $blueseal."/gainplan/gainplan";

        $opera = $blueseal."/gainplan/gainplan";
        $aggiungi = $blueseal."/gainplan/gainplan";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'pageURL' =>$pageURL,
            'operaURL' =>$opera,
            'aggiungiURL' =>$aggiungi,
            'page' => $this->page,
            'currentYear'=>$currentYear,
            'sidebar' => $this->sidebar->build(),
            'permission' => $permission
        ]);
    }
}