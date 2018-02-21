<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\application\AApplication;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CPaymentBillRepo;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CPaymentBillSendInvoiceMovements
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/02/2018
 * @since 1.0
 */
class CPaymentBillSendInvoiceMovements extends AAjaxController
{
    /**
     * @return string
     */
    public function post() {

    $paymentBillId = \Monkey::app()->router->request()->getRequestData('id');

    /** @var CPaymentBillRepo $paymentBillRepo */
    $paymentBillRepo = \Monkey::app()->repoFactory->create('PaymentBill');

    /** @var CPaymentBill $paymentBill */
    $paymentBill = $paymentBillRepo->findOneBy(['id'=>$paymentBillId]);

        foreach ($paymentBill->getDistinctPayments() as $key => $payment) {
            //$to = explode(';', $payment[0]->shopAddressBook->shop->referrerEmails);

            $name = $payment[0]->shopAddressBook->subject;

            $total = 0;
            foreach ($payment as $invoice) {
                $total += $invoice->getSignedValueWithVat();
            }


            /** @var CEmailRepo $mailRepo */
            $mailRepo = \Monkey::app()->repoFactory->create('Email');
            $mailRepo->newPackagedMail('friendpaymentinvoicemovements', 'no-reply@pickyshop.com', ['amministrazione@iwes.it'], [], [], ['paymentBill' => $paymentBill,
                'billId' => $paymentBillId,
                'name' => $name,
                'total' => $total,
                'payment' => $payment]);
        }

        return true;
    }

}

