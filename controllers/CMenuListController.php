<?php
namespace bamboo\blueseal\controllers ;

use bamboo\core\theming\CRestrictedAccessWidgetHelper ;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CMenuListController
 * @package bamboo\app\controllers
 */
class CMenuListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "menu_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/menu_list.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}