<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CTagEditController
 * @package bamboo\app\controllers
 */
class CTagExclusiveEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "tag_exclusive_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/tag_exclusive_edit.php');

        $tagId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $tag = \Monkey::app()->repoFactory->create('TagExclusive')->findOneBy(['id' => $tagId]);
        $tagTrans = \Monkey::app()->repoFactory->create('TagExclusiveTranslation')->findBy(['tagExclusiveId' => $tagId]);
        $shops  =  \Monkey::app()->repoFactory->create('Shop')->findAll();
        $storeHouses=\Monkey::app()->repoFactory->create('Storehouse')->findBy(['shopId'=>$tag->shopId]);

        $langs = $this->app->entityManagerFactory->create('Lang')->findAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'tag' => $tag,
            'tagTrans' => $tagTrans,
            'langs' => $langs,
            'storeHouses' =>$storeHouses,
            'shops' =>$shops,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $tagId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $tag = \Monkey::app()->repoFactory->create('TagExclusive')->findOneBy(['id' => $tagId]);
            $tagTransRepo = \Monkey::app()->repoFactory->create('TagExclusiveTranslation');
            foreach ($data as $k => $v) {
                if(strstr($k, 'tagExclusiveName_') && $v != '') {
                    $key = explode ('_',$k);
                    $tagTrans = $tagTransRepo->findOneBy(['tagExclusiveId'=>$tagId,'langId'=>$key[1]]);
                    if (isset($tagTrans)) {
                        $tagTrans->name = $v;
                        $tagTrans->update();
                    } else {
                        $tagTrans = $this->app->entityManagerFactory->create('TagExclusiveTranslation')->getEmptyEntity();
                        $tagTrans->tagExclusiveId = $tagId;
                        $tagTrans->langId = $key[1];
                        $tagTrans->name = $v;
                        $tagTrans->insert();
                    }
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
            if(isset($data['allShop'])) {
                $tag->allShop = 1;
            } else {
                $tag->allShop = 0;
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

            $tag->update();

            \Monkey::app()->repoFactory->commit();
            return true;
        } catch(\Throwable $e){

            \Monkey::app()->repoFactory->rollback();
            return false;

        }
    }
}