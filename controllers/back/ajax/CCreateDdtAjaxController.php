<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\pdfprint\CPrintDdt;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShop;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CShootingRepo;


/**
 * Class CCreateDdtAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 26/03/2018
 * @since 1.0
 */
class CCreateDdtAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {

        $printCode = function (CProduct $product){
            return $product->id.'-'.$product->productVariantId;
        };

        $printDescription = function (CProduct $product){
            return $product->printCpf().' - '.$product->productBrand->name;
        };


        $shootingId = \Monkey::app()->router->request()->getRequestData('shooting');
        $coll = \Monkey::app()->router->request()->getRequestData('coll');
        $carrier = \Monkey::app()->router->request()->getRequestData('carrier');

        if(empty($coll) || empty($carrier)) return "Inserire tutti i dati";

        /** @var CShootingRepo $sRepo */
        $sRepo = \Monkey::app()->repoFactory->create('Shooting');

        /** @var CDocumentRepo $documentRepo */
        $documentRepo = \Monkey::app()->repoFactory->create('Document');

        /** @var CShooting $shooting */
        $shooting = $sRepo->findOneBy(['id'=>$shootingId]);

        //shop information
        /** @var CShop $shop */
        $shop = $shooting->shop;
        $shopImageName = ($shop->name == "dalben" ? "dalben.png" : "logoiwes.jpg");
        $shopName = $shop->billingAddressBook->name;
        $shopAddress = $shop->billingAddressBook->address;
        $codeAddress = $shop->billingAddressBook->postcode.' '.$shop->billingAddressBook->city.' ('.$shop->billingAddressBook->province.')';
        $vatNumber = $shop->billingAddressBook->vatNumber;
        $phone = $shop->billingAddressBook->phone;

        //shooting info
        $ddtNumber = $documentRepo->findShootingFriendDdt($shooting);

        //date
        $date = $shooting->date;
        $date = new \DateTime($date);
        $newDate = $date->format('d-m-Y');

        //Cause
        /** @var CDocument $document */
        $document = $documentRepo->findOneBy(['id'=>$shooting->friendDdt]);
        $cause = $document->invoiceType->name;

        /** @var CObjectCollection $products */
        $products = $shooting->product;



        \Monkey::app()->vendorLibraries->load('pdfGenerator');

        $pdf = new CPrintDdt('P', 'mm', 'A4');
        // set document information
        $pdf->setInfo($carrier, $coll);
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
                                    <img src="https://cdn.iwes.it/assets/{$shopImageName}" alt="logo" height="80">
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.7;">
                                 <h4>$shopName</h4>
                                 <p>$shopAddress</p>
                                 <p>$codeAddress</p>
                                 <p>Italia</p>
                                 <p>$vatNumber</p>
                                 <p>TEL: $phone</p>
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
                                 <p>$cause</p>
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
                                 <h3>N° $ddtNumber - Data $newDate</h3>
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
                                     <h4>IWES snc</h4>
                                 <p>Via Cesare Pavese, 1 62100 Civitanova Marche (MC)</p>
                                 <p>Italia</p>
                             </span>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4; font-size: 14px;">
                                     <h3>Luogo di destinazione</h3>
                             </span>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <h4>IWES snc</h4>
                                 <p>Via Cesare Pavese, 1 62100 Civitanova Marche (MC)</p>
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
EOD;
        /** @var CProduct $product */
        foreach ($products as $product){

            $tbl .= <<<EOD
                <tr>
                    <td width="15%" style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p>{$printCode($product)}</p>
                             </span>
                    </td>
                    <td width="55%" align="left"
                        style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p>{$printDescription($product)}</p>
                             </span>
                    </td>
                    <td width="15%" style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p>pz.</p>
                             </span>
                    </td>
                    <td width="15%"
                        style="border-bottom: 1px solid #dbdbdb;">
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                     <p>1</p>
                             </span>
                    </td>
                </tr>
EOD;
        }
        $tbl .= <<<EOD
            </table>
        </td>
    </tr>
</table>
    
      
         
EOD;

        $pdf->SetAutoPageBreak(true, 55);
        $pdf->SetFooterMargin(55);
        $pdf->writeHTML($tbl, true, false, false, false, '');
        //Close and output PDF document
        \Monkey::app()->router->response()->setContentType('application/pdf');
        ob_end_clean();
        //$pdf->Output('spedizione.pdf', 'D');

        \Monkey::app()->router->response()->setContentType('application/pdf');

        $ddt = $pdf->Output('DdtFriend.pdf', 'S');

        if($documentRepo->insertInvoiceBinWithRowFile($shooting->friendDdt, $ddtNumber,$ddt)){
            $res = "Documento di trasporto inserito con successo";
        };

        //Aggiorna colli
        $sRepo->updatePieces($shooting->id, $coll);

        return $res;

    }

}