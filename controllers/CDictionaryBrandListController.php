<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CDictionaryBrandListController
 * @package bamboo\app\controllers
 */
class CDictionaryBrandListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "dictionary_brand_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/dictionary_brand_list.php');

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}