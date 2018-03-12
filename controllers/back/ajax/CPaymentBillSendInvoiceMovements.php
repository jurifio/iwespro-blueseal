<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\application\AApplication;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\exceptions\BambooMailException;
use bamboo\core\intl\CLang;
use bamboo\core\theming\CMailerHelper;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CInvoice;
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
            $to = explode(';', $payment[0]->shopAddressBook->shop->referrerEmails);

            $name = $payment[0]->shopAddressBook->subject;

            $total = 0;
            foreach ($payment as $invoice) {
                $total += $invoice->getSignedValueWithVat();
            }


            /** @var CEmailRepo $mailRepo */
            $mailRepo = \Monkey::app()->repoFactory->create('Email');
            $mailRepo->newPackagedMail('friendpaymentinvoicemovements', 'no-reply@pickyshop.com', $to, [], ['amministrazione@iwes.it'], ['paymentBill' => $paymentBill,
                'billId' => $paymentBillId,
                'name' => $name,
                'total' => abs($total),
                'payment' => $payment]);
        }

        return true;
    }

    /**
     * @return string
     * @throws BambooMailException
     */
    public function get(){

        $paymentBillId = \Monkey::app()->router->request()->getRequestData('id');

        /** @var CPaymentBillRepo $paymentBillRepo */
        $paymentBillRepo = \Monkey::app()->repoFactory->create('PaymentBill');

        /** @var CPaymentBill $paymentBill */
        $paymentBill = $paymentBillRepo->findOneBy(['id'=>$paymentBillId]);
        $fattura = [];
        $i = 0;
        foreach ($paymentBill->getDistinctPayments() as $key => $payment) {
            $total = 0;
            /** @var CDocument $invoice */
            foreach ($payment as $invoice) {
                $total += $invoice->getSignedValueWithVat();
                $friend = $invoice->shopAddressBook->subject;
            }

            $passedVars = [
                'paymentBill' => $paymentBill,
                'billId' => $paymentBillId,
                'total' => abs($total),
                'payment' => $payment
            ];
            $app = new CMailerHelper(\Monkey::app(), $passedVars);
            $mailPackage = 'friendpaymentinvoicemovements';

            $templateMailRoot = \Monkey::app()->rootPath() . '/client/public/content/mail' . '/' . $mailPackage;
            $lang = isset($passedVars['lang']) ? $passedVars['lang'] : \Monkey::app()->getLang()->getLang();
            $template = $templateMailRoot . '/' . $mailPackage . '.php';

            if (is_dir($templateMailRoot) && is_readable($templateMailRoot)) {
                $css = file_get_contents($templateMailRoot . '/' . $mailPackage . '.css');
                try {
                    $data = json_decode(file_get_contents($templateMailRoot . '/' . $mailPackage . '.' . $lang . '.json'));
                } catch (\Throwable $e) {
                    $data = json_decode(file_get_contents($templateMailRoot . '/' . $mailPackage . '.it.json'));
                }
            } else {
                throw new BambooMailException("Email package not present or readable: %s", [$templateMailRoot]);
            };


            extract($passedVars);
            ob_start();
            include $template;
            $body = ob_get_clean();

            return $body;
        }
    }


    private function convert_multi_array($array) {
        $out = implode("<br />",array_map(function($a) {return implode(" | ",$a);},$array));
        return $out;
    }
}

