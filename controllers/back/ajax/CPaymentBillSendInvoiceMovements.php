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

        foreach ($paymentBill->getDistinctPayments() as $key => $payment) {
            $to = explode(';', $payment[0]->shopAddressBook->shop->referrerEmails);

            $name = $payment[0]->shopAddressBook->subject;

            $total = 0;
            foreach ($payment as $invoice) {
                $total += $invoice->getSignedValueWithVat();
            }

        }

        //--------------------------

        \Monkey::app()->vendorLibraries->load('pdfGenerator');


        $pdf = new \TCPDF('P', 'mm', 'A4');
// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 003');
        $pdf->SetSubject('TCPDF Tutorial');
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
        $tbl = <<<EOD
<table>
    <tr>
        <td align="left" width="50%" style="font-size:9px;font-family:Helvetica,Arial,sans-serif;">
            <table border="0" cellpadding="2" cellspacing="2" align="left" data-editable="image"
                   data-mobile-stretch="0" width="100%">
                <tr>
                    <td style="border-top: 1px solid #ffffff; border-right: 2px solid #ffffff; border-left: 1px solid #ffffff; border-bottom: 2px solid #ffffff;">
                        <table>
                            <tr>
                                <td>
                                    <img src="https://cdn.iwes.it/assets/logoiwes.jpg" alt="logo" height="80">
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.7;">
                                 <h4>IWES snc</h4>
                                 <p>Via Cesare Pavese, 1</p>
                                 <p>62010 CIVITANOVA MARCHE (MC)</p>
                                 <p>Italia</p>
                                 <p>P.IVA: 01865380438</p>
                                 <p>CF: 01865380438</p>
                                 <p>TEL: +39.0733.471365</p>
                                 <p><a href="mailto:billing@iwes.it">billing@iwes.it</a></p>
                             </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table border="0" cellpadding="4" cellspacing="2" align="left" data-editable="image"
                   data-mobile-stretch="0" width="100%">
                <tr>
                    <td style="border-top: 1px solid #ffffff; border-right: 2px solid #ffffff; border-left: 1px solid #ffffff; background-color: #ffffff;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.2; font-size: 12px;">
                                     <h3>Causale del trasporto</h3>
                             </span>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4;">
                                 <p>RESO ORDINE 11745258</p>
                             </span>
                    </td>
                </tr>
            </table>
        </td>
        <td align="left" width="50%" style="font-size:9px;font-family:Helvetica,Arial,sans-serif;">
            <table border="0" cellpadding="4" cellspacing="2" align="center" data-editable="image"
                   data-mobile-stretch="0" width="100%">
                <tr>
                    <td style="border-top: 1px solid #ffffff; border-right: 2px solid #ffffff; border-left: 1px solid #ffffff; border-bottom: 2px solid #ffffff;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.8;">
                                        <h3>DOCUMENTO DI TRASPORTO</h3>
                                 <h3>N°29/2018 - Data 08/02/2018</h3>
                             </span>
                    </td>
                </tr>
            </table>
            <table border="0" cellpadding="4" cellspacing="2" align="left" data-editable="image"
                   data-mobile-stretch="0" width="100%">
                <tr>
                    <td style="border-top: 1px solid #dbdbdb; border-right: 1px solid #dbdbdb; border-left: 1px solid #dbdbdb; border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4; font-size: 14px;">
                                     <h3>Destinatario</h3>
                             </span>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <h4>Vietti & Levorato s.r.l.</h4>
                                 <p>Corso Repubblica, 38 28041 Arona (NO)</p>
                                 <p>Italia</p>
                             </span>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4; font-size: 14px;">
                                     <h3>Luogo di destinazione</h3>
                             </span>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <h4>Vietti & Levorato s.r.l.</h4>
                                 <p>Corso Repubblica, 38 28041 Arona (NO)</p>
                                 <p>Italia</p>
                             </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr width="100%">
        <td align="left" width="100%" style="font-size:8px;font-family:Helvetica,Arial,sans-serif;">
            <table border="0" cellpadding="0" cellspacing="0" align="center" data-editable="image"
                   data-mobile-stretch="0" width="100%">
                <tr>
                    <td width="15%"
                        style="background-color: #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4;">
                                     <h4>Codice</h4>
                             </span>
                    </td>
                    <td width="55%" align="left"
                        style="background-color: #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4;">
                                     <h4>Descrizione</h4>
                             </span>
                    </td>
                    <td width="15%"
                        style="background-color: #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4;">
                                     <h4>UM</h4>
                             </span>
                    </td>
                    <td width="15%"
                        style="background-color: #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4;">
                                     <h4>Quantità</h4>
                             </span>
                    </td>
                </tr>
                <tr>
                    <td width="15%" style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p></p>
                             </span>
                    </td>
                    <td width="55%" align="left"
                        style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p>Red Wing - art. 00875 - variante marrone</p>
                                     <p>Reso ordine 11790377 - Taglia 9</p>
                             </span>
                    </td>
                    <td width="15%" style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p></p>
                             </span>
                    </td>
                    <td width="15%"
                        style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p>1</p>
                             </span>
                    </td>
                </tr>
                <tr>
                    <td width="15%" style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p></p>
                             </span>
                    </td>
                    <td width="55%" align="left"
                        style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p>Givenchy - art. 00548 - variante nero</p>
                                     <p>Reso ordine 11790487 - Taglia 40</p>
                             </span>
                    </td>
                    <td width="15%" style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p></p>
                             </span>
                    </td>
                    <td width="15%"
                        style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p>1</p>
                             </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
    
      
         
EOD;
        $pdf->writeHTML($tbl, true, false, false, false, '');
//Close and output PDF document
        //\Monkey::app()->router->response()->setContentType('application/pdf');
        return $pdf->Output('spedizione.pdf', 'S');

    }

}

