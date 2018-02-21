<?php
namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\repositories\CEmailRepo;

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
class CPaymentBillSubmit extends AAjaxController
{
    public function put()
    {
        $paymentBillId = $this->app->router->request()->getRequestData('paymentBillId');
        /** @var CPaymentBill $paymentBill */
        $paymentBill = \Monkey::app()->repoFactory->create('PaymentBill')->findOneByStringId($paymentBillId);
        \Monkey::app()->repoFactory->create('PaymentBill')->submitPaymentBill($paymentBill,new \DateTime());


        if($paymentBill->getTotal() > 0) {
            foreach ($paymentBill->getDistinctPayments() as $key => $payment) {
                $to = explode(';', $payment[0]->shopAddressBook->shop->referrerEmails);
                $name = $payment[0]->shopAddressBook->subject;

                $total = 0;
                foreach ($payment as $invoice) {
                    $total += $invoice->getSignedValueWithVat();
                }

                /*$this->app->mailer->prepare('friendpaymentmail','no-reply', $to,[],['amministrazione@iwes.it'],['paymentBill'=>$paymentBill,
                                                                                        'name'=>$name,
                                                                                        'total'=>$total,
                                                                                        'payment'=>$payment]);
                $this->app->mailer->send();*/


                /** @var CEmailRepo $mailRepo */
                $mailRepo = \Monkey::app()->repoFactory->create('Email');
                $mailRepo->newPackagedMail('friendpaymentmail', 'no-reply@pickyshop.com', $to, [], ['amministrazione@iwes.it'], ['paymentBill' => $paymentBill,
                    'name' => $name,
                    'total' => $total,
                    'payment' => $payment]);
            }
        }


        return true;
    }
}