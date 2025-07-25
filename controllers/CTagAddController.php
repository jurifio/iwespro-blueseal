<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CTagAddController
 * @package redpanda\app\controllers
 */
class CTagAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "tag_add";


    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/tag_add.php');

        $sortings = \Monkey::app()->repoFactory->create('SortingPriority')->findAll();
        $langs = \Monkey::app()->repoFactory->create('Lang')->findAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'sortingPriority' => $sortings,
            'langs' => $langs,
            'page'=>$this->page,
            'sidebar'=>$this->sidebar->build()
        ]);
    }

    /**
     * @return void
     */
    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $tagRepo = \Monkey::app()->repoFactory->create('Tag');
            $tag = $tagRepo->getEmptyEntity();
            $tagTransRepo = \Monkey::app()->repoFactory->create('TagTranslation');
            $tagTrans = $tagTransRepo->getEmptyEntity();
            $lang =[];

            foreach ($data as $k => $v) {
                if(strstr($k, 'tagName_') && $v != '') {
                    $key = explode ('_',$k);
                    $lang[$key[1]] = $v;
                } else {
                    if ($k == 'slug') {
                        $tag->slug = $v;
                    }
                    if ($k == 'sortingId') {
                        $tag->sortingPriorityId = $v;
                    }
                }

	            if(isset($data['isPublic'])) {
		            $tag->isPublic = 1;
	            } else {
		            $tag->isPublic = 0;
	            }
                if(isset($data['isDefault'])) {
                    $tag->isDefault = 1;
                } else {
                    $tag->isDefault = 0;
                }
            }
            $tagId = $tag->insert();
            $tagTrans->tagId = $tagId;
            foreach ($lang as $key => $val) {
                $tagTrans->langId = $key;
                $tagTrans->name = $val;
                $tagTrans->insert();
            }

            return $tagId;
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}