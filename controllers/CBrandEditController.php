<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
/**
 * Class CBrandEditController
 * @package bamboo\app\controllers
 */
class CBrandEditController extends CBrandManageController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";
    /**
     * @var string
     */
    protected $pageSlug = "product_brand_edit";

    /**
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/brand_edit.php');
        $user = $this->app->getUser();
        $allShops = $user->hasPermission('allShops');
        // Se non è allshop devono essere visualizzate solo le linee relative allo shop e solo a un certo punto di avanzamento
        $currentUser=$this->app->getUser()->getId();
        if ($this->app->getUser()->hasPermission('allShops')) {
            $allShops='1';
        }else{
            $allShops='2';
        }
        $brandId =  $this->app->router->request()->getRequestData('id');
        $brandEdit = \Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id'=>$brandId]);


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'brandEdit' => $brandEdit,
            'page'=>$this->page,
            'currentUser'=>$currentUser,
            'allShops'=>$allShops,
            'sidebar'=>$this->sidebar->build()
        ]);
    }
}