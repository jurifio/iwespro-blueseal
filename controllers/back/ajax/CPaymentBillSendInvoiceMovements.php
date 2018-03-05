<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\application\AApplication;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\intl\CLang;
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
     *
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



        foreach ($payment as $invoice){
            if ($invoice->note == "ANNULLATA") {continue;}

            $fattura[$friend.'_'.$i]["numero"] = $invoice->number;
            $fattura[$friend.'_'.$i]["data"] = STimeToolbox::EurFormattedDate($invoice->date);

            if($invoice->getSignedValueWithVat() < 0) {
                $fattura[$friend.'_'.$i]["valore"] = abs($invoice->getSignedValueWithVat());
            } else {
                $fattura[$friend.'_'.$i]["valore"] = $invoice->getSignedValueWithVat() *-1;
            }

            $i++;
        }




        }

        $list = $this->convert_multi_array($fattura);


        \Monkey::app()->vendorLibraries->load('pdfGenerator');


        $pdf = new \TCPDF('P', 'mm', 'A4');
// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Iwes');
        $pdf->SetTitle('Lista delle fatture');
        $pdf->SetSubject('Lista fatture');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        $pdf->setPrintHeader(false);
// set header and footer fonts
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
        $pdf->SetMargins(5, 10, 5);
// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set font
        $pdf->SetFont('times', '', 12);
// add a page
        $pdf->AddPage();
        $tbl = "Estratto conto della distinta n".$paymentBill->id.'<br />'.$list;


        $pdf->writeHTML($tbl, true, false, false, false, '');
//Close and output PDF document
        return base64_encode($pdf->Output('test.pdf', 'S'));
    }


    private function convert_multi_array($array) {
        $out = implode("<br />",array_map(function($a) {return implode(" | ",$a);},$array));
        return $out;
    }
}

