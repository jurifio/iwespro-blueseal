<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
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
class CPaymentBillSubmit extends AAjaxController
{
    public function put()
    {
        $paymentBillId = $this->app->router->request()->getRequestData('paymentBillId');
        /** @var CPaymentBill $paymentBill */
        $paymentBill = $this->app->repoFactory->create('PaymentBill')->findOneByStringId($paymentBillId);
        $this->app->repoFactory->create('PaymentBill')->submitPaymentBill($paymentBill,new \DateTime());

        foreach ($paymentBill->getDistinctPayments() as $key=>$payment) {
            $to = explode(',',$payment[0]->shopAddressBook->shop->referrerEmails);
            $name = $payment[0]->shopAddressBook->subject;

            $total = 0;
            foreach ($payment as $invoice) {
                $total+=$invoice->getSignedValueWithVat();
            }

            $this->app->mailer->prepare('friendpaymentmail','no-reply', $to,[],[],['paymentBill'=>$paymentBill,
                                                                                    'name'=>$name,
                                                                                    'total'=>$total,
                                                                                    'payment'=>$payment]);
        }


        return true;
    }
}