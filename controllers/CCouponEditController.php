<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use PDO;
use PDOException;

/**
 * Class CCouponEditController
 * @package bamboo\app\controllers
 */
class CCouponEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "coupon_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/coupon_edit.php');

        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $couponRepo = \Monkey::app()->repoFactory->create('Coupon');
        $coupon = $couponRepo->findOneBy(['id'=>$couponId]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'coupon' => $coupon,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $couponRepo = \Monkey::app()->repoFactory->create('Coupon');
        $coupon = $couponRepo->findOneBy(['id'=>$couponId]);

        foreach ($data as $k => $v) {
            $coupon->{$k} = $v;
            if($k=='couponTypeId'){
                $couponType=\Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id'=>$v]);
                $amountType=$couponType->amountType;
                $coupon->amountType=$amountType;
            }
        }


        $couponRepo->update($coupon);


    }
}