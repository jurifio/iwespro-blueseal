<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CCouponTypeEditController
 * @package bamboo\app\controllers
 */
class CCouponTypeEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "coupontype_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/coupontype_edit.php');

        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $couponRepo = \Monkey::app()->repoFactory->create('CouponType');
        $coupon = $couponRepo->findOneBy(['id'=>$couponId]);

        $possValids =[];
        $possValids[0] = '1 anno';
        $possValids[1] = '1 mese';
        $possValids[2] = '7 giorni';

        $possValidity = [];
        $possValidity[0] = 'P1Y';
        $possValidity[1] = 'P1M';
        $possValidity[2] = 'P7D';

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'coupon' => $coupon,
            'possValids' => $possValids,
            'possValidity' => $possValidity,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $couponId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $couponType = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id'=>$couponId]);

        foreach ($data as $k => $v) {
            $couponType->{$k} = $v;
        }
        $couponType->update();

        foreach ($couponType->couponTypeHasTag as $couponTypeHasTag) {
            $couponTypeHasTag->delete();
        }

        foreach ($data['tags'] as $tag) {
            $couponTypeHasTag = \Monkey::app()->repoFactory->create('CouponTypeHasTag')->getEmptyEntity();
            $couponTypeHasTag->tagId = $tag;
            $couponTypeHasTag->couponTypeId = $couponType->id;
            $couponTypeHasTag->insert();
        }
    }
}