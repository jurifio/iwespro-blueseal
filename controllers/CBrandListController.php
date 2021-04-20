<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBrandListController
 * @package bamboo\app\controllers
 */
class CBrandListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_brand_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/brand_list.php');
        $allShops = $this->app->getUser()->hasPermission('allShops');
        $blueseal = $this->app->baseUrl(false).'/blueseal';
        $addUrl = $blueseal."/prodotti/brand/aggiungi";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'addUrl' => $addUrl,
            'allShops'=>$allShops,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}