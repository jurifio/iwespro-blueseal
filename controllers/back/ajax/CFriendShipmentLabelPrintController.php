<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CShipment;
use bamboo\domain\entities\CShipmentFault;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CShipmentManageController
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
class CFriendShipmentLabelPrintController extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $shipmentId = $this->app->router->request()->getRequestData('shipmentId');

        /** @var CShipment $shipment */
        $shipment = $this->app->repoFactory->create('Shipment')->findOne([$shipmentId]);
        if($shipment === null || $shipment->scope != CShipment::SCOPE_SUPPLIER_TO_US) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return "shipment not valid";
        }

        \Monkey::app()->vendorLibraries->load('pdfGenerator');
        $pdf = new \TCPDF('P', 'mm', 'A5');

        $n = rand(0, 10000);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Iwes s.n.c.');
        $pdf->SetTitle('Pickyshop Shipping Tag');
        $pdf->SetSubject('Shipment n° ' . $n);
        $pdf->SetKeywords('Shipment, Pickyshop, Package, Order, Tag');

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');

        $pdf->AddPage('P', 'A5', true, true);

        // define barcode style
        $style = array(
            'position' => '',
            'align' => 'R',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => 'C',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        $soggetto = 'Mittente';
        $nome = $shipment->fromAddress->subject;
        $via = $shipment->fromAddress->address;
        $cap = $shipment->fromAddress->postcode;
        $citta = $shipment->fromAddress->city;;
        $provincia = $shipment->fromAddress->province;

        $pdf->writeHTML("<h2>$soggetto:</h2><br />" .
            "<h1><strong>$nome</strong></h1><br />" .
            "<h2>$via<br />" .
            "$cap $citta ($provincia)<br />" .
            "</h2>");
        $pdf->Ln(20);
        $pdf->write1DBarcode(str_pad($shipment->id,10,'0',STR_PAD_LEFT), 'I25', '', '', '', 30, 0.4, $style, 'R');
        $pdf->Ln(20);

        $soggetto = 'Destinatario';
        $nome = $shipment->toAddress->subject;
        $via = $shipment->toAddress->address;
        $cap = $shipment->toAddress->postcode;
        $citta = $shipment->toAddress->city;;
        $provincia = $shipment->toAddress->province;

        $pdf->writeHTML("<div style=\"text-align:right;\"><h2>$soggetto:</h2><br />" .
            "<h1><strong>$nome</strong></h1><br />" .
            "<h2>$via<br />" .
            "$cap $citta $provincia<br />" .
            "</h2></span>");

        $pdf->Ln();

        \Monkey::app()->router->response()->setContentType('application/pdf');
        return $pdf->Output('spedizione_'.$shipment->id.'.pdf', 'S');
    }
}