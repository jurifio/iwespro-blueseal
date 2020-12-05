<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CTagExclusiveAddController
 * @package redpanda\app\controllers
 */
class CTagExclusiveAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "tag_exclusive_add";


    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/tag_exclusive_add.php');

        $langs = \Monkey::app()->repoFactory->create('Lang')->findAll();
        $shops  =  \Monkey::app()->repoFactory->create('Shop')->findAll();


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'langs' => $langs,
            'page'=>$this->page,
            'shops'=>$shops,
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
            $tagRepo = \Monkey::app()->repoFactory->create('TagExclusive');
            $tag = $tagRepo->getEmptyEntity();
            $tagTransRepo = \Monkey::app()->repoFactory->create('TagExclusiveTranslation');
            $tagTrans = $tagTransRepo->getEmptyEntity();
            $lang =[];

            foreach ($data as $k => $v) {
                if(strstr($k, 'tagExclusiveName_') && $v != '') {
                    $key = explode ('_',$k);
                    $lang[$key[1]] = $v;
                } else {
                    if ($k == 'slug') {
                        $tag->slug = $v;
                    }
                }
            }
	            if(isset($data['isPublic'])) {
		            $tag->isPublic = 1;
	            } else {
		            $tag->isPublic = 0;
	            }
                if(isset($data['isActive'])) {
                    $tag->isActive = 1;
                } else {
                    $tag->isActive = 0;
                }
                if(isset($data['storeHouseId'])) {
                    $tag->storeHouseId = $data['storeHouseId'];
                }
                if(isset($data['exclusiven'])){
                    $tag->exclusiven = $data['exclusiven'];
                }
                if(isset($data['shopId'])) {
                    $tag->shopId = $data['shopId'];
                }

            $tagId = $tag->insert();
            $tagTrans->tagExclusiveId = $tagId;
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