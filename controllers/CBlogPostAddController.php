<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CBlogPostAddController
 * @package bamboo\app\controllers
 */
class CBlogPostAddController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "blog_add";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/blog_add.php');

        $cats = $this->app->repoFactory->create('PostCategory')->findAll();
        $tags = $this->app->repoFactory->create('PostTag')->findAll();
        $statuses = [];
        foreach($this->app->repoFactory->create('PostStatus')->findAll() as $status) {
            if ($status->show) $statuses[$status->id] = $status->name;
        }

	    $defaultImage = '/assets/bs-dummy-16-9.png';

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'user'=>$this->app->getUser(),
            'cats'=>$cats,
	        'defaultImage' => $defaultImage,
            'tags'=>$tags,
            'statuses'=>$statuses,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {
        $newPostData = $this->app->router->request()->getRequestData();
        $coverImageData = $this->app->router->request()->getFiles();
        $fileFolder =  $this->app->rootPath().$this->app->cfg()->fetch('paths', 'dummyFolder') . '/';

        $files = $this->app->router->request()->getFiles();

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


        $postId = $postRepo->insert($Post);

        $PostTranslation->postId = $postId;
        $PostTranslation->blogId = $newPostData['Post.blogId'];

        $postRepo->setCategories($postId,$Post->blogId,explode(',',$newPostData['PostHasPostCategory.id']));
        $postRepo->setTags($postId,$Post->blogId,explode(',',$newPostData['PostHasPostTag.id']));
        $postTranslationRepo->insert($PostTranslation);

        $this->app->router->response()->autoRedirectTo($this->app->baseUrl(false)."/blueseal/blog/modifica?id=".$postId."&blogId=".$Post->blogId);
	    return;
    }
}