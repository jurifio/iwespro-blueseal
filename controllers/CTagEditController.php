<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CTagEditController
 * @package bamboo\app\controllers
 */
class CTagEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "tag_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/tag_edit.php');

        $tagId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $tag = $this->app->repoFactory->create('Tag')->findOneBy(['id' => $tagId]);
        $tagTrans = $this->app->repoFactory->create('TagTranslation')->findBy(['tagId' => $tagId]);

        $sortingPriority = $this->app->entityManagerFactory->create('SortingPriority')->findAll();
        $langs = $this->app->entityManagerFactory->create('Lang')->findAll();

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'tag' => $tag,
            'tagTrans' => $tagTrans,
            'langs' => $langs,
            'sortingPriority' => $sortingPriority,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $tagId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $this->app->dbAdapter->beginTransaction();
        try {
            $tag = $this->app->repoFactory->create('Tag')->findOneBy(['id' => $tagId]);
            $tagTransRepo = $this->app->repoFactory->create('TagTranslation');
            foreach ($data as $k => $v) {
                if(strstr($k, 'tagName_') && $v != '') {
                    $key = explode ('_',$k);
                    $tagTrans = $tagTransRepo->findOneBy(['tagId'=>$tagId,'langId'=>$key[1]]);
                    if (isset($tagTrans)) {
                        $tagTrans->name = $v;
                        $tagTrans->update();
                    } else {
                        $tagTrans = $this->app->entityManagerFactory->create('TagTranslation')->getEmptyEntity();
                        $tagTrans->tagId = $tagId;
                        $tagTrans->langId = $key[1];
                        $tagTrans->name = $v;
                        $tagTrans->insert();
                    }
                } else {
                    if ($k == 'slug') {
                        $tag->slug = $v;
                    }
                    if ($k == 'sortingId') {
                        $tag->sortingPriorityId = $v;
                    }
                }

            }

            $tag->update();

            $this->app->dbAdapter->commit();
            return true;
        } catch(\Exception $e){

            $this->app->dbAdapter->rollback();
            return false;

        }
    }
}