<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\application\AApplication;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\entities\CProduct;
use bamboo\utils\time\STimeToolbox;
use DateTime;

/**
 * Class CBillRegistryActivePaymentBillPayManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/05/2020
 * @since 1.0
 */
class CBillRegistryActivePaymentBillPayManage extends AAjaxController
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
        $amountPaid=$paymentBillData['amount'];
        $amount=$paymentBill->amount;
        $paymentBill->paymentDate = (new \DateTime($paymentBillData['paymentDate']))->format('Y-m-d H:i:s');
        if($amountPaid<$amount) {
            $paymentBill->statusId = 5;
        }else if($amountPaid>=$amount){
            $paymentBill->statusId = 2;
            $now=new \DateTime();
            $dateNow=$now->format('Y-m-d H:i:s');
            $paymetBill->submissionDate=$dateNow;
        }else{
            $paymentBill->statusId=5;
        }
        $paymentBill->update();
        $btt=\Monkey::app()->repoFactory->create('BillRegistryTimeTable')->findBy(['billRegistryActivePaymentSlipId'=>$paymentBillData['id']]);
        $invoiceIds=[];
        $tempInvoiceIds=0;
        foreach ($btt as $payments){
            $invoiceId=$payments->billRegistryInvoiceId;
            if($tempInvoiceIds!=$invoiceId){
                array_push($invoiceIds,$invoiceId);
                $tempInvoiceIds=$invoiceId;
            }
            if($amountPaid<=0){
                break;
            }else{
                $amountPayment = $payments->amountPayment;
                if ($amount < $amountPayment){
                    $payments->amountPaid=$amount;
                    $amount-=$amount;
                }else{
                    $payments->amountPaid=$amountPayment;
                    $amount -= $amountPayment;
                }
                    $payments->datePayment = $paymentBillData['paymentDate'];
                $payments->update();


            }
        }
        foreach ($invoiceIds as $invoice){
            $findBillRegistryInvoiceTimeTable=\Monkey::app()->repoFactory->create('BillRegistryTimeTable')->findBy(['billRegistryInvoiceId'=>$invoice]);
            $findBillRegistryInvoice=\Monkey::app()->repoFactory->create('BillRegistryInvoice')->findOneBy(['id'=>$invoice]);
            $grossTotal=$findBillRegistryInvoice->grossTotal;
            foreach($findBillRegistryInvoiceTimeTable as $payment){
                if($payment->amountPaid!=null){
                    $grossTotal-=$amountPayment;
                }

            }
            if($grossTotal<=0){
                $findBillRegistryInvoice->statusId=3;
                $findBillRegistryInvoice->update();
            }
        }

        return true;
    }
}