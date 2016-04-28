<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\slugify\CSlugify;
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

        return $view->render([
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
        $fileFolder =  $this->app->rootPath().$this->app->cfg()->fetch('paths', 'blogImages') . '/';

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

	    if(!empty($coverImageData) && isset($coverImageData['PostTranslation.coverImage'])) {
		    $s = new CSlugify();
		    $pathinfo = pathinfo($coverImageData['PostTranslation.coverImage']['name']);
		    $uploadfile = rand(0, 9999999999) . '-' .$s->slugify($pathinfo['filename']).'.'. $pathinfo['extension'];
		    if (!rename($coverImageData['PostTranslation.coverImage']['tmp_name'], $fileFolder . $uploadfile)) throw new \Exception();
		    $PostTranslation->blogId = $uploadfile;
	    }

        //$postRepo->setCategories($postId,$Post->blogId,explode(',',$newPostData['PostHasPostCategory.id']));
        //$postRepo->setTags($postId,$Post->blogId,explode(',',$newPostData['PostHasPostTag.id']));
        $postTranslationRepo->insert($PostTranslation);

        $this->app->router->response()->autoRedirectTo($this->app->baseUrl(false)."/blueseal/blog/modifica?id=".$postId."&blogId=".$Post->blogId);
	    return;
    }
}