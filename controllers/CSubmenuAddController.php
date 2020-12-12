<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CTagAddController
 * @package redpanda\app\controllers
 */
class CSubmenuAddController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "submenu_add";


    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/submenu_add.php');
        $menus=\Monkey::app()->repoFactory->create('Menu')->findAll();
        $langs = \Monkey::app()->repoFactory->create('Lang')->findAll();
        $menuNavType=\Monkey::app()->repoFactory->create('menuNavType')->findAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'langs' => $langs,
            'page'=>$this->page,
            'menus'=>$menus,
            'menuNavType'=>$menuNavType,
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
            $menuRepo = \Monkey::app()->repoFactory->create('MenuNav');
            $menu = $menuRepo->getEmptyEntity();
            $menuTransRepo = \Monkey::app()->repoFactory->create('menuNavTranslation');
            $menuTrans = $menuTransRepo->getEmptyEntity();
            $lang =[];

            foreach ($data as $k => $v) {
                if (strstr($k,'tagName_') && $v != '') {
                    $key = explode('_',$k);
                    $lang[$key[1]] = $v;
                } else {
                    if ($k == 'slug') {
                        $menu->slug = $v;
                    }

                }
            }
                if(isset($data['typeId'])){
                    $menu->typeId=$data['typeId'];
                }else{
                    return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Selezione Tipo Menu Non eseguita inserito</i>';
                }

                if(isset($data['menuId'])){
                    $menu->menuId=$data['menuId'];
                }else{
                    return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Menu Padre Non Selezionato</i>';
                }
                if(isset($data['elementId'])){
                    $menu->elementId=$data['elementId'];
                }else{
                    $menu->elementId=0;
                }

	            if(isset($data['chooseOperation'])) {
                    if($data['chooseOperation']=='1'){
                        $menu->captionImage=$data['photoUrl'];
                    }else{
                        $menu->captionImage='/assets/px.png';
                    }
                }else{
	                $menu->captionImage='/assets/px.png';
                }
	            if(isset($data['captionTitle'])){
	                $menu->captionTitle=$data['captionTitle'];
                }else{
                    return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Titolo menu non inserito</i>';
                }
                if(isset($data['captionLink'])){
                    switch($data['typeId']){
                        case "1":
                            $caption = strtolower($data['captionTitle']);
                            $caption = str_replace(' ','-',$caption);
                            $menu->captionLink=$caption;
                            break;
                        case "2":
                            $menu->captionLink=$data['captionLink'];
                            break;
                        case "3":
                            $productCategory=\Monkey::app()->repoFactory->create('ProductCategory')->findOneBy(['id'=>$data['elementId']]);
                            $caption=$productCategory->slug.'-'.$productCategory->id;
                            $menu->captionLink=$caption;
                            break;
                        case "4":
                            $tag=\Monkey::app()->repoFactory->create('Tag')->findOneBy(['id'=>$data['elementId']]);
                            $caption=strtolower($tag->slug);
                            $caption = str_replace(' ','-',$caption);
                            $captionFinal=$caption.'-'.$tag->id;
                            $menu->captionLink=$captionFinal;
                            break;
                        case "5";
                            $tagExclusive=\Monkey::app()->repoFactory->create('TagExclusive')->findOneBy(['id'=>$data['elementId']]);
                            $caption=strtolower($tagExclusive->slug);
                            $caption = str_replace(' ','-',$caption);
                            $captionFinal=$caption.'-'.$tagExclusive->id;
                            $menu->captionLink=$captionFinal;
                        break;
                        case "6";
                        $menu->captionLink='brands';
                        break;
                    }
                }

            $menuId = $menu->insert();
            $menuTrans->menuNavTranslationId = $menuId;
            foreach ($lang as $key => $val) {
                $menuTrans->langId = $key;
                $menuTrans->captionTitle = $val;
                $menuTrans->captionText = $val;
                $menuTrans->insert();
            }

            return $menuId;
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseProcessingError();
            $this->app->router->response()->sendHeaders();
        }
    }
}