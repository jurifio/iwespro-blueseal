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
 * Class CBillRegistryActivePaymentBillManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/04/2020
 * @since 1.0
 */
class CBillRegistryActivePaymentBillManage extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        $paymentBillId = $this->app->router->request()->getRequestData('paymentBillId');
        $paymentBill = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip')->findOneByStringId($paymentBillId);
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
        $paymentBill = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip')->findOneByStringId($paymentBillData['id']);

        if($paymentBill->statusId<6) throw new BambooInvoiceException('Non puoi modificare una distinta già sottomessa');

        $paymentBill->paymentDate = $paymentBillData['paymentDate'] && !empty($paymentBillData['paymentDate']) ? STimeToolbox::DbFormattedDate($paymentBillData['paymentDate']) : $paymentBill->paymentDate;
        $paymentBill->update();
        $btt=\Monkey::app()->repoFactory->create('BillRegistryTimeTable')->findBy(['billRegistryActivePaymentSlipId'=>$paymentBillData['id']]);
        foreach ($btt as $payments){
            $payments->dateEstimated=$paymentBillData['paymentDate'] && !empty($paymentBillData['paymentDate']) ? STimeToolbox::DbFormattedDate($paymentBillData['paymentDate']) : $payments->dateEstimated;
            $payments->update();
        }
        return true;
    }
}