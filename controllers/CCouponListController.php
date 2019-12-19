<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCouponListController
 * @package bamboo\app\controllers
 */
class CCouponListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "coupon_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/coupon_list.php');
        $isChkActive = \Monkey::app()->router->request()->getRequestData('isActive');
        if($isChkActive==null) {
            $isChkActive = '';
        }
            $isChkUser= \Monkey::app()->router->request()->getRequestData('isUser');
        if ($isChkUser==null){
            $isChkUser='';
        }
        //$isChkActive=1;
        //$isChkUser=1;

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'isChkUser'=>$isChkUser,
            'isChkActive' =>$isChkActive,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}