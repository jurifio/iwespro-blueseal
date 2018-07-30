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

        $cartAbandonedEmailParam=\Monkey::app()->repoFactory->create('CartAbandonedEmailParam')->findAll();
        $i=0;
        $collectCartAbandonedEmailParam=[];
        foreach($cartAbandonedEmailParam as $cartAbandonedEmailParams){
            $i=$i+1;
            $collectCartAbandonedEmailParam[$i]['firstTemplateId']=$cartAbandonedEmailParams->firstTemplateId;
            $collectTemplate=\Monkey::app()->repoFactory->create('NewsletterTemplate')->findOneBy(['id'=>$cartAbandonedEmailParams->firstTemplateId]);
            $collectCartAbandonedEmailParam[$i]['id']=$cartAbandonedEmailParams->id;
            $collectCartAbandonedEmailParam[$i]['templateName']=$collectTemplate->name;
            $collectCartAbandonedEmailParam[$i]['firstTemplateId']=$cartAbandonedEmailParams->firstTemplateId;
            $collectCartAbandonedEmailParam[$i]['firstTimeEmailSendDay']=$cartAbandonedEmailParams->firstTimeEmailSendDay;
            $collectCartAbandonedEmailParam[$i]['firstTimeEmailSendHour']=$cartAbandonedEmailParams->firstTimeEmailSendHour;
            $collectCartAbandonedEmailParam[$i]['secondTemplateId']=$cartAbandonedEmailParams->secondTemplateId;
            $collectCartAbandonedEmailParam[$i]['secondTimeEmailSendDay']=$cartAbandonedEmailParams->secondTimeEmailSendDay;
            $collectCartAbandonedEmailParam[$i]['secondTimeEmailSendHour']=$cartAbandonedEmailParams->secondTimeEmailSendHour;
            $collectCartAbandonedEmailParam[$i]['thirdTemplateId']=$cartAbandonedEmailParams->thirdTemplateId;
            $collectCartAbandonedEmailParam[$i]['thirdTimeEmailSendDay']=$cartAbandonedEmailParams->thirdTimeEmailSendDay;
            $collectCartAbandonedEmailParam[$i]['thirdTimeEmailSendHour']=$cartAbandonedEmailParams->thirdTimeEmailSendHour;
            $collectCartAbandonedEmailParam[$i]['couponTypeId']=$cartAbandonedEmailParams->couponTypeId;
            $collectCartAbandonedEmailParam[$i]['selectMailCouponSend']=$cartAbandonedEmailParams->selectMailCouponSend;
            if($collectCartAbandonedEmailParam[$i]['couponTypeId']!='0') {
                $collectCouponType = \Monkey::app()->repoFactory->create('CouponType')->findOneBy(['id' => $collectCartAbandonedEmailParam[$i]['couponTypeId']]);
                $collectCartAbandonedEmailParam[$i]['amount'] = $collectCouponType->amount;
                $collectCartAbandonedEmailParam[$i]['amountType'] = $collectCouponType->amountType;
                $typeCollectValidity = $collectCouponType->validity;
                switch ($typeCollectValidity) {
                    case 'P1Y':
                        $collectCartAbandonedEmailParam[$i]['validity'] = 'P1Y';
                        $collectCartAbandonedEmailParam[$i]['labelValidity'] = 'Un Anno';
                        break;
                    case 'P1M':
                        $collectCartAbandonedEmailParam[$i]['validity'] = 'P1M';
                        $collectCartAbandonedEmailParam[$i]['labelValidity'] = 'Un Mese';
                        break;
                    case 'P7D':
                        $collectCartAbandonedEmailParam[$i]['validity'] = 'P7D';
                        $collectCartAbandonedEmailParam[$i]['labelValidity'] = 'Una Settimana';
                        break;
                }

                $collectCartAbandonedEmailParam[$i]['validForCartTotal']=$collectCouponType->validForCartTotal;
                $collectCartAbandonedEmailParam[$i]['hasFreeShipping']=$collectCouponType->hasFreeShipping;
                $collectCartAbandonedEmailParam[$i]['hasFreeReturn']=$collectCouponType->hasFreeReturn;
            }
        }

$template=\Monkey::app()->repoFactory->create('NewsletterTemplate')->findAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'collectCartAbandonedEmailParam'=>$collectCartAbandonedEmailParam,
            'template'=>$template,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}