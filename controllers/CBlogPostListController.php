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
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/blog_list.php');

        $blueseal = $this->app->baseUrl(false).'/blueseal';
        $addUrl = $blueseal."/blog/aggiungi";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'addUrl' => $addUrl,
            'sidebar' => $this->sidebar->build()
        ]);
    }

	/**
	 * @return bool
	 */
	public function delete()
	{
		$a = $this->app->router->request()->getRequestData();
		foreach ($a as $key => $row) {
			if(strpos($key,'row') !== 0 ) continue;
			$ids = explode('__',$row);
			$post = \Monkey::app()->repoFactory->create('Post')->findOne(['id'=>$ids[0],'blogId'=>$ids[1]]);
			$post->postStatusId = 3;
			$post->update();
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function put()
	{
		$a = $this->app->router->request()->getRequestData();
		foreach ($a as $key => $row) {
			if(strpos($key,'row') !== 0 ) continue;
			$ids = explode('__',$row);
			$post = \Monkey::app()->repoFactory->create('Post')->findOne(['id'=>$ids[0],'blogId'=>$ids[1]]);
			$post->postStatusId = 2;
			$post->update();
		}

		return true;
	}


}