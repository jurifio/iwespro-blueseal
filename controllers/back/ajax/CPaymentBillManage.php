<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\application\AApplication;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\entities\CProduct;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CPaymentBillListAjaxController
 * @package bamboo\blueseal\controllers\ajax
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
class CPaymentBillManage extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        $paymentBillId = $this->app->router->request()->getRequestData('paymentBillId');
        $paymentBill = \Monkey::app()->repoFactory->create('PaymentBill')->findOneByStringId($paymentBillId);
        return json_encode($paymentBill);
    }

    public function post()
    {
        $paymentBill = \Monkey::app()->repoFactory->create('PaymentBill')->getEmptyEntity();

        $paymentBill->paymentDate = STimeToolbox::DbFormattedDate($this->app->router->request()->getRequestData('paymentDate'));
        $paymentBill->amount = 0;
        $paymentBill->id = $paymentBill->insert();
        return json_encode($paymentBill);

    }

    public function put()
    {
        $paymentBillData = $this->app->router->request()->getRequestData('paymentBill');
        /** @var CPaymentBill $paymentBill */
        $paymentBill = \Monkey::app()->repoFactory->create('PaymentBill')->findOneByStringId($paymentBillData['id']);
        if($paymentBill->isSubmitted()) throw new BambooInvoiceException('Non puoi modificare una distinta già sottomessa');

        $paymentBill->paymentDate = $paymentBillData['paymentDate'] && !empty($paymentBillData['paymentDate']) ? STimeToolbox::DbFormattedDate($paymentBillData['paymentDate']) : $paymentBill->paymentDate;
        $paymentBill->update();
        return true;
    }
}