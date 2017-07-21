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
    protected $box1Template = '<img class="image-responsive" src="https://cdn.iwes.it/jimmy-choo/72551-3909421-001-562.jpg">';
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
        $pdf->SetAutoPageBreak(true,$paperTopMargin);


        foreach ($shipmentsId as $shipmentId) {
            /** @var CShipment $shipment */
            $shipment = $this->app->repoFactory->create('Shipment')->findOne([$shipmentId]);
            foreach ($shipment->orderLine as $orderLine) {
                /** @var COrderLine $orderLine */
                $pdf->AddPage('P', 'A4', true, true);
                /** INFO */
                $iwes = true;
                $address = $orderLine->order->shipmentAddress;
                $note = $orderLine->order->note;
                $payment = $orderLine->order->isPayed();
                $paymentMethod = $orderLine->order->orderPaymentMethod->name;
                $isContrassegno = $orderLine->order->orderPaymentMethod->name == 'contrassegno';
                $orderLineNumber = $orderLine->id;
                $orderLineTotal = $orderLine->order->orderLine->count();
                $brand = $orderLine->productSku->product->productBrand->name;
                $category = $orderLine->productSku->product->getLocalizedProductCategories();
                $friend = $orderLine->productSku->shopHasProduct->shop->name;
                $cpf = $orderLine->productSku->product->printCpf();
                $size = $orderLine->productSku->productSize->name;
                $linePrice = $orderLine->activePrice;
                $orderPrice = $orderLine->order->netTotal;

                $photo = $orderLine->productSku->product->getPhoto(1, CProductPhoto::SIZE_MEDIUM);

                $shipmentCode = $shipmentId;

                $shelf = $orderLine->warehouseShelfPosition->warehouseShelf->name;
                $position = $orderLine->warehouseShelfPosition->name;

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
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box1Template, 1, 'C', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);
                $xPos = $cellWidth + ($cellMargin * 2) + $paperSideMargin;
                $cellHeight = ($cellHeight - ($cellMargin * 3)) / 4;
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box2Template, 1, 'C', false, 0, $xPos, $yPos, true, 0, true, true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);
                $yPos = $yPos + $cellHeight + ($cellMargin) ;
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box3Template, 1, 'C', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);
                $yPos = $yPos + $cellHeight + ($cellMargin);
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box4Template, 1, 'C', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);
                $yPos = $yPos + $cellHeight + ($cellMargin);
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box5Template, 1, 'C', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');
                //var_dump($xPos,$yPos,$cellHeight);

                $xPos = $paperSideMargin;
                $yPos = $yPos + $cellHeight + ($cellMargin);
                $cellWidth = ($w / 2) - ($cellMargin);
                $cellHeight = ((146 / 2) / 4) * 3;
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box6Template, 1, 'C', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');

                $xPos = $cellWidth + ($cellMargin * 2) + $paperSideMargin;
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box7Template, 1, 'C', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');

                $yPos = $yPos + $cellHeight + ($cellMargin);
                $xPos = $paperSideMargin;
                $cellWidth = $w;
                $cellHeight = ((146 / 2) / 4);
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box8Template, 1, 'L', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');

                $yPos = $yPos + $cellHeight + ($cellMargin);
                $cellWidth = ($w / 2) - ($cellMargin);
                $cellHeight = $h - $yPos - $cellMargin;
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box9Template, 1, 'L', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');

                $xPos = $cellWidth + ($cellMargin * 2) + $paperSideMargin;
                $pdf->MultiCell($cellWidth, $cellHeight, $this->box10Template, 1, 'C', false, 0, $xPos, $yPos, true, 0, true,true, $yPos, 'M');

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