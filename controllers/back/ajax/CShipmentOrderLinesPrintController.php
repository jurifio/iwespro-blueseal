<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CProductPhoto;
use bamboo\domain\entities\CShipment;
use bamboo\domain\entities\CShipmentFault;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CShipmentOrderLinesPrintController
 * @package bamboo\controllers\back\ajax
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
class CShipmentOrderLinesPrintController extends AAjaxController
{

    protected $box2Template = '<img class="image-responsive" src="https://cdn.iwes.it/assets/logoIwes.png">';
    protected $box3Template = '<div>Cod. Spedizione:<br /><p style="font-size:30px; line-height: 10px"><strong>00000000835</strong></p></div>';
    protected $box4Template = '<span>Ordine Pagato con  <strong>Contrassegno</strong><br/>
                                    Prezzo Riga: 432,00 <br/>
                                    Totale Ordine: 1004,00<br />
                                    Ordine 100245665, riga:<br /><strong>1</strong> di <strong>3</strong>
                                    </span>';
    protected $box5Template = '<span style="line-height:32px;font-size:30px"><br /><strong>CONTRASSEGNO</strong></span>';
    protected $box6Template = '<span style="font-size:20px"><br />Brand: <strong>Saucony</strong><br />
                                     Categoria: <strong>Donna/Calzature/qualcosaltro</strong><br />
                                     Friend: <strong>Cartechini</strong></span>';
    protected $box7Template = '<span style="font-size:20px"><br />CPF: <strong>1254hb # 001</strong><br />
                                     Taglia: <strong>39</strong><br />
                                     SKU: <strong>1234313-12345379</strong></span>';
    protected $box8Template = '<span>il cliente vuole il profumo all\'arancia</span>';
    protected $box9Template = '<span><strong>Fabrizio Marconi</strong><br/>
                                    via Molise 18<br/>
                                    Civitanova Marche (62012)<br />
                                    Macerate, Italia</span>';
    protected $box10Template = '<span style="line-height:50px;font-size:85px"><br /><strong>C-30</strong></span>';

    /**
     * @return string
     */
    public function get()
    {
        $shipmentsId = $this->app->router->request()->getRequestData('shipmentsId');

        \Monkey::app()->vendorLibraries->load('pdfGenerator');
        $pdf = new \TCPDF('P', 'mm', 'A5');

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Iwes s.n.c.');
        $pdf->SetTitle('Pickyshop Shipping Tag');
        $pdf->SetSubject('Shipments');
        $pdf->SetKeywords('Shipment, Pickyshop, Package, Order, OrderLine, Tag');

        $paperWidth = 210;
        $paperHeight = 297;
        $paperTopMargin = 10;
        $paperSideMargin = 10;
        $w = $paperWidth - ($paperSideMargin * 2);
        $h = $paperHeight + 1 - $paperTopMargin;

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins($paperSideMargin, $paperTopMargin, $paperSideMargin, true);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
        $pdf->SetAutoPageBreak(true, $paperTopMargin);


        foreach ($shipmentsId as $shipmentId) {
            /** @var CShipment $shipment */
            $shipment = $this->app->repoFactory->create('Shipment')->findOne([$shipmentId]);
            foreach ($shipment->orderLine as $orderLine) {
                /** @var COrderLine $orderLine */
                $pdf->AddPage('P', 'A4', true, true);
                /** INFO */
                $iwes = true;
                $address = $orderLine->order->shipmentAddress;
                $subject = $address->name . ' ' . $address->surname;
                $address1 = $address->address;
                $address2 = $address->extra;
                $city = $address->city;
                $postcode = $address->postcode;
                $province = $address->province;
                $country = $address->country->name;

                $address = $orderLine->order->billingAddress;
                $Bsubject = $address->name . ' ' . $address->surname;
                $Baddress1 = $address->address;
                $Baddress2 = $address->extra;
                $Bcity = $address->city;
                $Bpostcode = $address->postcode;
                $Bprovince = $address->province;
                $Bcountry = $address->country->name;


                $note = $orderLine->order->note;
                $payment = $orderLine->order->isPayed();
                $paymentMethod = $orderLine->order->orderPaymentMethod->name;
                $isContrassegno = $orderLine->order->orderPaymentMethod->name == 'contrassegno';
                $isBilled = $orderLine->order->billingAddress->isBilling;
                $orderNumber = $orderLine->orderId;
                $orderLineNumber = $orderLine->id;
                $orderLineTotal = $orderLine->order->orderLine->count();
                $brand = $orderLine->productSku->product->productBrand->name;
                $category = $orderLine->productSku->product->getLocalizedProductCategories();
                $friend = $orderLine->productSku->shopHasProduct->shop->name;
                $cpf = $orderLine->productSku->product->printCpf();
                $size = $orderLine->productSku->productSize->name;
                $linePrice = $orderLine->activePrice;
                $orderPrice = $orderLine->order->netTotal;

                $sku = $orderLine->productSku->printFullSku();
                $photo = $orderLine->productSku->product->getPhoto(1, CProductPhoto::SIZE_MEDIUM);

                $shipmentCode = $shipmentId;

                $position = $orderLine->warehouseShelfPosition->printPosition();

                // set cell padding
                $cellPadding = 2;
                $pdf->setCellPaddings($cellPadding, $cellPadding, $cellPadding, $cellPadding);

                // set cell margins
                $cellMargin = 2;
                $pdf->setCellMargins(0, 0, $cellMargin, $cellMargin);

                $yPos = $paperTopMargin;
                $xPos = $paperSideMargin;

                $cellWidth = ($w / 2) - ($cellMargin);
                $cellHeight = 146;
                $pdf->MultiCell($cellWidth, $cellHeight, "<img class=\"image-responsive\" src=\"https://cdn.iwes.it/$photo\">", 1, 'C', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);
                $xPos = $cellWidth + ($cellMargin * 2) + $paperSideMargin;
                $cellHeight = ($cellHeight - ($cellMargin * 3)) / 4;
                $pdf->MultiCell($cellWidth, $cellHeight, "<img class=\"image-responsive\" src=\"https://cdn.iwes.it/assets/logoIwes.png\">", 1, 'C', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);
                $yPos = $yPos + $cellHeight + ($cellMargin);
                $pdf->MultiCell($cellWidth, $cellHeight, "<div>Cod. Spedizione:<br /><p style=\"font-size:30px; line-height: 10px\"><strong>$shipmentCode</strong></p></div>", 1, 'C', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);
                $yPos = $yPos + $cellHeight + ($cellMargin);
                $pdf->MultiCell($cellWidth, $cellHeight, "<span>Ordine" . ($payment ? " " : " <strong style=\"font-size:20px\">NON</strong> ") . "Pagato con  <strong>$paymentMethod</strong><br/>
                                                                Valore Riga: $linePrice <br/>
                                                                Totale Ordine: $orderPrice<br />
                                                                Ordine $orderNumber, riga:<br /><strong>$orderLineNumber</strong> di <strong>$orderLineTotal</strong>
                                                                </span>", 1, 'L', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);
                $yPos = $yPos + $cellHeight + ($cellMargin);
                $pdf->MultiCell($cellWidth, $cellHeight, "<span><br /></span><span style=\"font-size:30px\">" .
                                                                ($isContrassegno ? "<strong>CONTRASSEGNO</strong><br />" : "") .
                                                                ($isBilled ? "<strong>FATTURA</strong>" : "") .
                                                             "</span>"
                    , 1, 'C', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);

                $yPos = $yPos + $cellHeight + ($cellMargin);
                $xPos = $paperSideMargin;
                $cellWidth = $w;
                $cellHeight = ((146 / 2) / 4);
                $pdf->MultiCell($cellWidth, $cellHeight, "<span>$note</span>", 1, 'L', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');

                $xPos = $paperSideMargin;
                $yPos = $yPos + $cellHeight + ($cellMargin);
                $cellWidth = ($w / 2) - ($cellMargin);
                $cellHeight = ((146 / 2) / 4) * 2;
                $pdf->MultiCell($cellWidth, $cellHeight, "<span><strong>SPEDIZIONE: </strong><br />
                                                                <strong>$subject</strong><br/>
                                                                $address1<br/>
                                                                $address2<br/>
                                                                $city ($postcode)<br />
                                                                $province, $country</span>", 1, 'L', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');

                $xPos = $cellWidth + ($cellMargin * 2) + $paperSideMargin;
                $pdf->MultiCell($cellWidth, $cellHeight, "<span><strong>FATTURAZIONE: </strong><br />
                                                                <strong>$Bsubject</strong><br/>
                                                                $Baddress1<br/>
                                                                $Baddress2<br/>
                                                                $Bcity ($Bpostcode)<br />
                                                                $Bprovince, $Bcountry</span>", 1, 'L', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');

                $yPos = $yPos + $cellHeight + ($cellMargin);
                $xPos = $paperSideMargin;
                $cellWidth = ($w / 2) - ($cellMargin);
                $cellHeight = $h - $yPos - $cellMargin;
                $pdf->MultiCell($cellWidth, $cellHeight, "<style>span{font-size: 17px}span strong{ font-size:21px };</style>
                                                                <span><strong>$brand</strong><br />
                                                             $category<br style=\"line-height:35px\" />
                                                             <strong>$friend</strong><br style=\"line-height:35px\" />
                                                                CPF:   <strong>$cpf</strong><br />
                                                                Taglia:    <strong>$size</strong><br />
                                                                SKU:   <strong>$sku</strong></span>", 1, 'L', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');

                $xPos = $cellWidth + ($cellMargin * 2) + $paperSideMargin;
                $pdf->MultiCell($cellWidth, $cellHeight, "<span style=\"line-height:70px;font-size:85px\"><br /><strong>$position</strong></span>", 1, 'C', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');

                //$pdf->writeHTMLCell($w,$h,$x,$y,$this->box1Template,1,0,false,true,'C');
                //$pdf->writeHTMLCell($w,$h,$x,$y,$this->box2Template,1,0,false,true,'C');

                // set color for background
                $pdf->SetFillColor(220, 255, 220);

            }
        }

        \Monkey::app()->router->response()->setContentType('application/pdf');
        return $pdf->Output('righe_ordine.pdf', 'S');
    }
}