<?php
namespace bamboo\blueseal\controllers ;

use bamboo\core\theming\CRestrictedAccessWidgetHelper ;
use bamboo\ecommerce\views\widget\VBase;


/**
 * Class CPageListController
 * @package bamboo\app\controllers
 */
class CPageListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "page_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/page_list.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}