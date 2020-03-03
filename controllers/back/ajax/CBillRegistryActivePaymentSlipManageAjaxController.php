<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CBillRegistryActivePaymentSlipManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/03/2020
 * @since 1.0
 */
class CBillRegistryActivePaymentSlipManageAjaxController extends AAjaxController
{
    public function put()
    {
        try {


            $data = $this->app->router->request()->getRequestData();
            $paymentBillId = $data['paymentBillId'];
            $recipientId = $data['recipientId'];
            $billRegistryActivePaymentSlipId = $data['documentId'];
            $paymentBillRepo = \Monkey::app()->repoFactory->create('PaymentBill');
            $billRegistryPaymentActiveSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
            $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
            $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
            $brpas = $billRegistryPaymentActiveSlipRepo->findOneBy(['id' => $billRegistryActivePaymentSlipId]);
            $amountActive = $brpas->amount;
            $p = $paymentBillRepo->findOneBy(['id' => $paymentBillId]);
            $amountPassive = $p->amount;
            $amountInvoice = 0;
            if($amountPassive>$amountActive){
                return 'Distinta Passiva  maggiore di quella Attiva';
            }
            $i = 1;
            $brtt = $billRegistryTimeTableRepo->findBy(['billRegistryActivePaymentSlipId' => $billRegistryActivePaymentSlipId]);
            if ($brtt != null) {
                foreach ($brtt as $payment) {
                    $amountActive -= $amountPassive;
                    if ($i == 1) {
                        $bri = $billRegistryInvoiceRepo->findOneBy(['id' => $payment->billRegistryInvoiceId]);
                        $amountInvoice = $bri->grossTotal;
                    }
                    $date = new \DateTime();
                    $datePayment = $date->format('Y-m-d H:i:s');
                    $ratePayment = $payment->amountPayment;
                    if ($ratePayment <= $amountPassive) {
                        $payment->amountPaid = $ratePayment;
                        $payment->datePayment = $datePayment;
                        $amountPassive -= $ratePayment;
                        $amountInvoice -= $ratePayment;
                        $payment->update();
                        if($amountPassive<=0){
                            $p->isPaid = 1;
                            $p->note = 'compensata con distinta Attiva n. ' . $billRegistryActivePaymentSlipId;
                            $p->update();
                        }
                    } elseif ($ratePayment > $amountPassive) {
                        $payment->amountPaid = $amountPassive;
                        $amountPassive -= $amountPassive;
                        $amountInvoice -= $amountPassive;
                        $payment->update();
                        if($amountPassive<=0){
                            $p->isPaid = 1;
                            $p->note = 'compensata con distinta Attiva n. ' . $billRegistryActivePaymentSlipId;
                            $p->update();
                        }
                    } elseif ($amountPassive <= 0) {
                        $payment->amountPaid = 0;
                        $payment->update();
                        $amountInvoice -= 0;
                        $p->isPaid = 1;
                        $p->note = 'compensata con distinta Attiva n. ' . $billRegistryActivePaymentSlipId;
                        $p->update();

                    }
                    $i++;
                }
                if ($amountInvoice <= 0) {
                    $briUpdate = $billRegistryInvoiceRepo->findOneBy(['id' => $payment->billRegistryInvoiceId]);
                    $briUpdate->statusId = 3;
                    $briUpdate->update();
                } elseif ($amountInvoice > 0) {
                    $briUpdate = $billRegistryInvoiceRepo->findOneBy(['id' => $payment->billRegistryInvoiceId]);
                    $briUpdate->statusId = 4;
                    $briUpdate->update();
                }
                if ($amountActive <= 0) {
                    $brpas->statusId = 2;
                    $brpas->datePayment = $datePayment;
                } else {
                    $brpas->statusId = 5;
                }
                $brpas->paymentBillId = $paymentBillId;
                $brpas->recipientId = $recipientId;
                $brpas->update();
            }


            return 'Associazione e Aggiornamento Eseguiti con successo';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CBillRegistryActivePaymentSlip','error','Association PaymentBill',$e,'');
            return $e;

        }
    }
}