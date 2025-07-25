<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CTagEditController
 * @package bamboo\app\controllers
 */
class CSubmenuEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "submenu_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/submenu_edit.php');

        $menuNavId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $menuNav = \Monkey::app()->repoFactory->create('MenuNav')->findOneBy(['id' => $menuNavId]);
        $menuNavTrans = \Monkey::app()->repoFactory->create('MenuNavTranslation')->findBy(['menuNavTranslationId' => $menuNavId]);
        $menus=\Monkey::app()->repoFactory->create('Menu')->findAll();
        $menuNavType=\Monkey::app()->repoFactory->create('MenuNavType')->findAll();
        $langs = $this->app->entityManagerFactory->create('Lang')->findBy(['isActive'=>1]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'menus' => $menus,
            'menuNav'=>$menuNav,
            'menuNavTrans' => $menuNavTrans,
            'langs' => $langs,
            'menuNavType'=>$menuNavType,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $menuId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $menu = \Monkey::app()->repoFactory->create('Menu')->findOneBy(['id' => $menuId]);
            $menuTransRepo = \Monkey::app()->repoFactory->create('MenuTranslation');
            foreach ($data as $k => $v) {
                if(strstr($k, 'tagName_') && $v != '') {
                    $key = explode ('_',$k);
                    $menuTrans = $menuTransRepo->findOneBy(['menuTranslationId'=>$menuId,'langId'=>$key[1]]);
                    if (isset($menuTrans)) {
                        $menuTrans->name = $v;
                        $menuTrans->update();
                    } else {
                        $menuTrans = $this->app->entityManagerFactory->create('MenuTranslation')->getEmptyEntity();
                        $menuTrans->menuTranslationId = $menuId;
                        $menuTrans->langId = $key[1];
                        $menuTrans->name = $v;
                        $menuTrans->insert();
                    }
                } else {
                    if ($k == 'slug') {
                        $menu->slug = $v;
                    }

                }
            }
            if(isset($data['order'])) {
                $menu->order = $data['order'];
            }
            if(isset($data['menuName'])){
                $menu->name=$data['menuName'];
            }
            if(isset($data['level'])){
                $menu->level=$data['level'];
            }



            $menu->update();

            \Monkey::app()->repoFactory->commit();
            return true;
        } catch(\Throwable $e){

            \Monkey::app()->repoFactory->rollback();
            return false;

        }
    }
}