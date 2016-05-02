<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBlogPostEditController
 * @package bamboo\app\controllers
 */
class CBlogPostTrashController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "blog_trash";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/blog_trash.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
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
			$post = $this->app->repoFactory->create('Post')->findOne(['id'=>$ids[0],'blogId'=>$ids[1]]);
			$post->postStatusId = 1;
			$post->update();
		}

		return true;
	}
}