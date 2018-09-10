<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBlogPostListController
 * @package bamboo\app\controllers
 */
class CWorkBlogPostListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "work_blog_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/work_blog_list.php');

        $posts = \Monkey::app()->repoFactory->create('Post')->findBy(['blogId' => 2, 'postStatusId' => 2]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'posts' => $posts,
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }


}