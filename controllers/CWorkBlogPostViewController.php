<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBlogPostListController
 * @package bamboo\app\controllers
 */
class CWorkBlogPostViewController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "work_blog_post";

    public function get()
    {
        $view = new VBase(array());
        $id = \Monkey::app()->router->getMatchedRoute()->getComputedFilter("id");
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/work_blog_post.php');

        $post = \Monkey::app()->repoFactory->create('Post')->findOneBy(["id" =>$id]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'post' => $post,
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }


}