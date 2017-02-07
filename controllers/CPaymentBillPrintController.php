<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\base\CArrayCollection;
use bamboo\core\base\CObjectCollection;
use bamboo\core\base\CStdCollectibleItem;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CPaymentBillPrintController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CPaymentBillPrintController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "aztec_print";

    public function get()
    {
        $paymentBill = $this->app->repoFactory->create('PaymentBill')->findOneByStringId($this->app->router->getMatchedRoute()->getComputedFilter('id'));
        \Monkey::app()->router->response()->setContentType('application/xml');
        return $paymentBill->toXml();
    }
}