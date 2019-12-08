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

        $cats = \Monkey::app()->repoFactory->create('PostCategory')->findAll();
        $tags = \Monkey::app()->repoFactory->create('PostTag')->findAll();
        $pageTranslationRepo=\Monkey::app()->repoFactory->create('PageTranslation');
        $statuses = [];
        foreach(\Monkey::app()->repoFactory->create('PostStatus')->findAll() as $status) {
            if ($status->show) $statuses[$status->id] = $status->name;
        }

        $blogs = [];
        foreach (\Monkey::app()->repoFactory->create('Blog')->findAll() as $blog){
            $blogs[$blog->id] = $blog->name;
        }
        $pag = [];
        foreach (\Monkey::app()->repoFactory->create('Page')->findAll() as $pagg){
            $pageTranslation=$pageTranslationRepo->findOneBy(['pageId'=>$pagg->id,'langId'=>1]);
            if($pageTranslation!=null) {
                $pag[$pagg->id] = $pageTranslation->title;
            }else{
                continue;
            }
        }

	    $defaultImage = '/assets/bs-dummy-16-9.png';

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'blogs' => $blogs,
            'pag'=> $pag,
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

        $postRepo = \Monkey::app()->repoFactory->create('Post');
        $postTranslationRepo = \Monkey::app()->repoFactory->create('PostTranslation');
        $pageRepo=\Monkey::app()->repoFactory->create('Page');



        $PostTranslation = $postTranslationRepo->getEmptyEntity();
        $Post = $postRepo->getEmptyEntity();

        foreach ($newPostData as $k => $v) {
            $tableField = explode('.',$k);
            if (is_int(stripos($k,'PostHasPostCategory')) ||is_int(stripos($k,'PostPage')) || is_int(stripos($k,'PostHasPostTag'))) {
                continue;
            }
            ${$tableField[0]}->{$tableField[1]} = $v;
        }
		\Monkey::app()->repoFactory->beginTransaction();
        try {
	        $postId = $postRepo->insert($Post);
	        $PostTranslation->postId = $postId;
	        $PostTranslation->blogId = $newPostData['Post.blogId'];
	        $valuePostId=$newPostData['PostPage.pageId'];
	        if($newPostData['Post.blogId']==3){
	            if($newPostData['PostPage.pageId']!=null){
	                $page=$pageRepo->findOneBy(['id'=>$valuePostId]);
	                $page->postId = $postId;
	                $page->update();
                }

            }

	        if(!empty($coverImageData) && isset($coverImageData['PostTranslation.coverImage'])) {
		        $s = new CSlugify();
		        $pathinfo = pathinfo($coverImageData['PostTranslation.coverImage']['name']);
		        $uploadfile = rand(0, 9999999999) . '-' .$s->slugify($pathinfo['filename']).'.'. $pathinfo['extension'];
		        if (!rename($coverImageData['PostTranslation.coverImage']['tmp_name'], $fileFolder . $uploadfile)) throw new \Exception();
		        $PostTranslation->coverImage = $uploadfile;
	        }

	        $postRepo->setCategories($postId,$Post->blogId,explode(',',$newPostData['PostHasPostCategory.id']));
	        $postRepo->setTags($postId,$Post->blogId,explode(',',$newPostData['PostHasPostTag.id']));
	        $postTranslationRepo->insert($PostTranslation);
	        \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e) {
			\Monkey::app()->repoFactory->rollback();
	        throw $e;
        }
	    
        $this->app->router->response()->autoRedirectTo($this->app->baseUrl(false)."/blueseal/blog/modifica?id=".$postId."&blogId=".$Post->blogId);
	    return;
    }
}