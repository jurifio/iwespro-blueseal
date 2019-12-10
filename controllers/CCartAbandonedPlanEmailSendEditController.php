<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\base\CObjectCollection;

/**
 * Class CCartAbandonedPlanEmailSendEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/07/2018
 * @since 1.0
 */
class CCartAbandonedPlanEmailSendEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "cartabandonedplanemailsend_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/cartabandonedplanemailsend_edit.php');
        /** @var aRepo $cartAbandonedEmailParam */
        $rulesId =  \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');

        $cartAbandonedEmailParam=\Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findOneBy(['id'=>$rulesId]);
        $coupontTypeRepo=\Monkey::app()->repoFactory->create('CouponType');
        $collectCoupon1Type=$coupontTypeRepo->findOneBy(['id'=>$cartAbandonedEmailParam->coupon1TypeId]);
        $collectCoupon2Type=$coupontTypeRepo->findOneBy(['id'=>$cartAbandonedEmailParam->coupon2TypeId]);
        $collectCoupon3Type=$coupontTypeRepo->findOneBy(['id'=>$cartAbandonedEmailParam->coupon3TypeId]);



            $template=\Monkey::app()->repoFactory->create('NewsletterTemplate')->findAll();
            $couponType=\Monkey::app()->repoFactory->create('CouponType')->findAll();
            $shops=\Monkey::app()->repoFactory->create('Shop')->findAll();


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'cartAbandonedEmailParam'=>$cartAbandonedEmailParam,
            'template'=>$template,
            'collectCoupon1Type'=>$collectCoupon1Type,
            'collectCoupon2Type'=>$collectCoupon2Type,
            'collectCoupon3Type'=>$collectCoupon3Type,
            'couponType'=>$couponType,
            'shops'=>$shops,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}