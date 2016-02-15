<?php
namespace bamboo\blueseal\controllers

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCouponEventEditController
 * @package bamboo\app\controllers
 */
class CCouponEventEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "couponevent_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/couponevent_edit.php');

        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $couponRepo = $this->app->repoFactory->create('CouponEvent');
        $coupon = $couponRepo->findOneBy(['id'=>$couponId]);

        $em = $this->app->entityManagerFactory->create('CouponType');
        $couponTypes = $em->findAll('limit 9999');

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'coupon' => $coupon,
            'couponTypes' => $couponTypes,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $couponRepo = $this->app->repoFactory->create('CouponEvent');
        $coupon = $couponRepo->findOneBy(['id'=>$couponId]);

        foreach ($data as $k => $v) {
            $coupon->{$k} = $v;
        }

        $couponRepo->update($coupon);
    }
}