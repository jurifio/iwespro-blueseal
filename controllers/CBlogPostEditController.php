<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBlogPostEditController
 * @package bamboo\app\controllers
 */
class CBlogPostEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "blog_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/blog_edit.php');

	    $id = $this->app->router->request()->getRequestData('id');
	    $blogId = $this->app->router->request()->getRequestData('blogId');
	    $post = $this->app->repoFactory->create('Post')->findOne(['id'=>$id,'blogId'=>$blogId]);

	    $defaultImage = 'http://redpanda.clo.ud.it/dummyPictures/bs-dummy-16-9.png';

	    $cats = $this->app->repoFactory->create('PostCategory')->findAll();
	    $tags = $this->app->repoFactory->create('PostTag')->findAll();
	    $statuses = [];
	    foreach($this->app->repoFactory->create('PostStatus')->findAll() as $status) {
		    if ($status->show) $statuses[$status->id] = $status->name;
	    }

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
	        'post' => $post,
	        'cats' => $cats,
	        'statuses' => $statuses,
	        'defaultImage' => $defaultImage,
	        'tags' => $tags,
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

	public function put(){
		$newPostData = $this->app->router->request()->getRequestData();
		$coverImageData = $this->app->router->request()->getFiles();

		$postRepo = $this->app->repoFactory->create('Post');
		$postTranslationRepo = $this->app->repoFactory->create('PostTranslation');

		$PostTranslation = $postTranslationRepo->getEmptyEntity();
		$Post = $postRepo->getEmptyEntity();

		foreach ($newPostData as $k => $v) {
			$tableField = explode('.',$k);
			if (is_int(stripos($k,'PostHasPostCategory')) || is_int(stripos($k,'PostHasPostTag'))) {
				continue;
			}
			${$tableField[0]}->{$tableField[1]} = $v;
		}


		$postRepo->update($Post);

		$PostTranslation->postId = $Post->id;
		$PostTranslation->blogId = $newPostData['Post.blogId'];

		$postRepo->setCategories($Post->id,$Post->blogId,explode(',',$newPostData['PostHasPostCategory.id']));
		$postRepo->setTags($Post->id,$Post->blogId,explode(',',$newPostData['PostHasPostTag.id']));
		$postTranslationRepo->update($PostTranslation);

		return $this->get();
	}
}