<?php
namespace bamboo\blueseal\controllers ;

use bamboo\core\theming\CRestrictedAccessWidgetHelper ;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CTagListController
 * @package bamboo\app\controllers
 */
class CTagListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "tag_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/tag_list.php');

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}