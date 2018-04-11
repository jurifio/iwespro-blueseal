<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\pdfprint\CPrintDdt;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CShop;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CShootingRepo;
use bamboo\domain\repositories\CShopRepo;


/**
 * Class CCreatePickyDdtAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/04/2018
 * @since 1.0
 */
class CCreatePickyDdtAjaxController extends AAjaxController
{

    public function get(){
        $step = \Monkey::app()->router->request()->getRequestData('step');
        $shootingId = \Monkey::app()->router->request()->getRequestData('shooting');
        $res = [];

        if($step == 1){

            /** @var CDocumentRepo $dRepo */
            $dRepo = \Monkey::app()->repoFactory->create('Document');

            /** @var CShooting $shooting */
            $shooting = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id'=>$shootingId]);

            $dId = $dRepo->findShootingPickyDdt($shooting);

            if($dId == false){
                /** @var CSectionalRepo $sectionalRepo */
                $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');

                $nextDdt = $sectionalRepo->calculateNewSectionalCodeFromShop(null, 12);
                $res["nextDdt"] = $nextDdt;
            } else if (is_string($dId)){
                $res["oldDdt"] = $dId;
            }


        } else if($step == 2){

            /** @var CShootingBooking $sb */
            $sb = \Monkey::app()->repoFactory->create('ShootingBooking')->findOneBy(['shootingId'=>$shootingId]);

            /** @var CShopRepo $shopRepo */
            $shopRepo = \Monkey::app()->repoFactory->create('Shop');

            /** @var CShop $shop */
            $shop = $shopRepo->findOneBy(['id'=>$sb->shopId]);

            /** @var CObjectCollection $bAdd */
            $bAdd = $shop->shippingAddressBook;


            $i = 0;
            /** @var CAddressBook $sAdd */
            foreach ($bAdd as $sAdd){
                $res[$i]["id"] = $sAdd->id;
                $res[$i]["subject"] = $sAdd->subject;
                $res[$i]["address"] = $sAdd->address;
                $res[$i]["city"] = $sAdd->postcode.' '.$sAdd->city.' ('.$sAdd->province.')';
                $res[$i]["country"] = $sAdd->country->name;
                $i++;
            }
        }

        return json_encode($res);
    }


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
        $dest = \Monkey::app()->router->request()->getRequestData('dest');
        $destLoc = \Monkey::app()->router->request()->getRequestData('destLoc');
        $ddtN = \Monkey::app()->router->request()->getRequestData('ddt');

        if(empty($coll) || empty($carrier) || empty($dest) || empty($destLoc)) return "Inserire tutti i dati";

        /** @var CShootingRepo $sRepo */
        $sRepo = \Monkey::app()->repoFactory->create('Shooting');

        /** @var CDocumentRepo $documentRepo */
        $documentRepo = \Monkey::app()->repoFactory->create('Document');

        /** @var CShooting $shooting */
        $shooting = $sRepo->findOneBy(['id'=>$shootingId]);

        /** @var CAddressBook $destinatario */
        $destinatario = \Monkey::app()->repoFactory->create('AddressBook')->findOneBy(['id'=>$dest]);
        $destName = $destinatario->subject;
        $address = $destinatario->address;
        $city = $destinatario->postcode.' '.$destinatario->city.' ('.$destinatario->province.')';
        $country = $destinatario->country->name;

        /** @var CAddressBook $luogoDestinazione */
        $luogoDestinazione = \Monkey::app()->repoFactory->create('AddressBook')->findOneBy(['id'=>$destLoc]);
        $destNameL = $luogoDestinazione->subject;
        $addressL = $luogoDestinazione->address;
        $cityL = $luogoDestinazione->postcode.' '.$luogoDestinazione->city.' ('.$luogoDestinazione->province.')';
        $countryL = $luogoDestinazione->country->name;

        //date
        $date = $shooting->date;
        $date = new \DateTime($date);
        $newDate = $date->format('d-m-Y');



        /** @var CObjectCollection $products */
        $products = $shooting->product;

        /** @var CDocument $doc */
        $date = date("Y-m-d");
        $dateTime = new \DateTime($date);
        $year = $dateTime->format('Y');

        //posso trovare più documenti con questa caratteristica quindi cerco solo quelli fatti da picky
        /** @var CObjectCollection $docs */
        $docs = $documentRepo->findBy(['number' => $ddtN, 'year' => $year]);


        if($docs->isEmpty()){
            $invoiceId = $documentRepo->createPickyDDTDocument($ddtN);
        } else {
            $stop = false;
            /** @var CDocument $doc */
            foreach ($docs as $doc){
                //se userAddressRecipientId/shopRecipientId è vuoto vuol dire che è un documento creato da picky e quindi ok
                if(is_null($doc->userAddressRecipientId) && is_null($doc->shopRecipientId)){
                    $invoiceId = $doc->id;
                    $stop = true;
                    break;
                }
            }
            //se siamo qui è perché nonostante la collezione di oggetti non sia vuota, all'interno di questa non sono stati trovati documenti realizzati da pickyshop
            if(!$stop){
                $invoiceId = $documentRepo->createPickyDDTDocument($ddtN);
            }

        }

        //Cause
        /** @var CDocument $document */
        $document = $documentRepo->findOneBy(['id'=>$invoiceId]);
        $cause = $document->invoiceType->name;



        \Monkey::app()->vendorLibraries->load('pdfGenerator');

        $pdf = new CPrintDdt('P', 'mm', 'A4');
        // set document information
        $pdf->setInfo($carrier, $coll);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Pickyshop');
        $pdf->SetTitle('DDT');
        $pdf->SetSubject('DDT');
        $pdf->SetKeywords('DDT');
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
                                 <p>Italia</p>
                                 <p>P. Iva 01865380438</p>
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
                                 <h3>N° $ddtN - Data $newDate</h3>
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
                                 <h4>$destName</h4>
                                 <p>$address</p>
                                 <p>$city</p>
                                 <p>$country</p>
                             </span>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.4; font-size: 14px;">
                                     <h3>Luogo di destinazione</h3>
                             </span>
                             <span style="font-family:Helvetica,Arial,sans-serif;color:#000000; line-height:0.6;">
                                 <h4>$destNameL</h4>
                                 <p>$addressL</p>
                                 <p>$cityL</p>
                                 <p>$countryL</p>
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

        \Monkey::app()->router->response()->setContentType('application/pdf');

        $ddt = $pdf->Output('DdtFriend.pdf', 'S');

        if($documentRepo->insertInvoiceBinWithRowFile($invoiceId, $ddtN, $ddt)){
            $shooting->pickyDdt = $invoiceId;
            $shooting->update();
            $res = $invoiceId;
        };

        return $res;

    }

}