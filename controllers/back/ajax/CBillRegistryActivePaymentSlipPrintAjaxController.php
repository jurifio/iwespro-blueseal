<?php
namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\entities\CUserAddress;
use bamboo\domain\repositories\CPaymentBillRepo;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBillRegistryAcitvePaymentSlipPrintAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/06/2020
 * @since 1.0
 */
class CBillRegistryActivePaymentSlipPrintAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "active_slipinvoice_print";

    public function get()
    {
        $view = new VBase(array());
        $this->page = new CBlueSealPage($this->pageSlug,$this->app);
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/active_slipinvoice_print.php');

        $paymentBillId = \Monkey::app()->router->request()->getRequestData('id');

        /** @var CPaymentBillRepo $paymentBillRepo */
        $paymentBillRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');

        /** @var CPaymentBill $paymentBill */
        $paymentBill = $paymentBillRepo->findOneBy(['id' => $paymentBillId]);
        $total=number_format($paymentBill->amount,2,',','.');
        $payment=\Monkey::app()->repoFactory->create('BillRegistryTimeTable')->findBy(['billRegistryActivePaymentSlipId'=>$paymentBillId]);




            return $view->render([
                'app' => new CRestrictedAccessWidgetHelper($this->app),
                'page' => $this->page,
                'paymentBill' => $paymentBill,
                'billId' => $paymentBillId,
                'total' => $total,
                'payment' => $payment
            ]);


        }


}

