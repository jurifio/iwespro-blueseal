<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CTagAddController
 * @package redpanda\app\controllers
 */
class CMenuAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "menu_add";


    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/menu_add.php');

        $langs = \Monkey::app()->repoFactory->create('Lang')->findBy(['isActive'=>1]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
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
            $menuRepo = \Monkey::app()->repoFactory->create('Menu');
            $menu = $menuRepo->getEmptyEntity();
            $menuTransRepo = \Monkey::app()->repoFactory->create('menuTranslation');
            $menuTrans = $menuTransRepo->getEmptyEntity();
            $lang =[];

            foreach ($data as $k => $v) {
                if(strstr($k, 'tagName_') && $v != '') {
                    $key = explode ('_',$k);
                    $lang[$key[1]] = $v;
                } else {
                    if ($k == 'slug') {
                        $menu->slug = $v;
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
            }
            $menuId = $menu->insert();
            $menuTrans->menuTranslationId = $menuId;
            foreach ($lang as $key => $val) {
                $menuTrans->langId = $key;
                $menuTrans->name = $val;
                $menuTrans->insert();
            }

            return $menuId;
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}