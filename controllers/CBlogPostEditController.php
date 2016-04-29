<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\slugify\CSlugify;
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
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/blog_edit.php');

	    $id = $this->app->router->request()->getRequestData('id');
	    $blogId = $this->app->router->request()->getRequestData('blogId');
	    $post = $this->app->repoFactory->create('Post')->findOne(['id'=>$id,'blogId'=>$blogId]);

	    $defaultImage = '/assets/bs-dummy-16-9.png';

	    $cats = $this->app->repoFactory->create('PostCategory')->findAll();
	    $tags = $this->app->repoFactory->create('PostTag')->findAll();
	    $statuses = [];
	    $statuses['selected'] = $post->postStatusId;
	    foreach($this->app->repoFactory->create('PostStatus')->findAll() as $status) {
		    if ($status->show) $statuses[$status->id] = $status->name;
	    }

        return $view->render([
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
		$fileFolder =  $this->app->rootPath().$this->app->cfg()->fetch('paths', 'blogImages') . '/';

		$postRepo = $this->app->repoFactory->create('Post');

		$Post = $postRepo->findOneBy(['id'=>$newPostData['Post.id'],'blogId'=>$newPostData['Post.blogId']]);
		$PostTranslation = $Post->postTranslation->getFirst();

		foreach ($newPostData as $k => $v) {
			$tableField = explode('.',$k);
			if (is_int(stripos($k,'PostHasPostCategory')) || is_int(stripos($k,'PostHasPostTag'))|| is_int(stripos($k,'coverImage')) ) {
				continue;
			}
			${$tableField[0]}->{$tableField[1]} = $v;
		}

		$Post->update();

		if(!empty($coverImageData) && isset($coverImageData['PostTranslation.coverImage'])) {
			$s = new CSlugify();
			$pathinfo = pathinfo($coverImageData['PostTranslation.coverImage']['name']);
			$uploadfile = rand(0, 9999999999) . '-' .$s->slugify($pathinfo['filename']).'.'. $pathinfo['extension'];
			if (!rename($coverImageData['PostTranslation.coverImage']['tmp_name'], $fileFolder . $uploadfile)) throw new \Exception();
			$PostTranslation->coverImage = $uploadfile;
		}

		$PostTranslation->postId = $Post->id;
		$PostTranslation->blogId = $newPostData['Post.blogId'];
		$PostTranslation->update();

		$postRepo->setCategories($Post->id,$Post->blogId,explode(',',$newPostData['PostHasPostCategory.id']));
		$postRepo->setTags($Post->id,$Post->blogId,explode(',',$newPostData['PostHasPostTag.id']));

		return true;
	}
}