<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBlogPostListController
 * @package bamboo\app\controllers
 */
class CBlogPostListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "blog_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/blog_list.php');

        $blueseal = $this->app->baseUrl(false).'/blueseal';
        $addUrl = $blueseal."/blog/aggiungi";

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'addUrl' => $addUrl,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}