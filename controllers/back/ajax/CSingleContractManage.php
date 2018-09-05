<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDownloadFileFromDb;
use bamboo\domain\entities\CContracts;
use bamboo\domain\entities\CFoison;
use bamboo\domain\repositories\CContractsRepo;


/**
 * Class CSingleContractManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/09/2018
 * @since 1.0
 */
class CSingleContractManage extends AAjaxController
{
    public function get()
    {
        $idC = \Monkey::app()->router->request()->getRequestData('idC');

        /** @var CContractsRepo $contractsRepo */
        $contractsRepo = \Monkey::app()->repoFactory->create('Contracts');

        $contract = $contractsRepo->findOneBy(["id" => $idC]);
        $fason = $contract->foison;

        $genericC = \Monkey::app()->repoFactory->create('ContractText')->findOneBy(['type' => 'generic'])->text;

        $data = array(
            'name' => $fason->name,
            'surname' => $fason->surname
        );

        return $contractsRepo->getFullTextContract($genericC, $data);

    }


    public function put()
    {

        $idC = \Monkey::app()->router->request()->getRequestData('idC');
        $text = \Monkey::app()->router->request()->getRequestData('textContract');

        \Monkey::app()->vendorLibraries->load('pdfGenerator');
        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor("IWES SNC");
        $pdf->SetTitle("Contratto n. $idC");
        $pdf->SetSubject("Contratto n. $idC");
        $pdf->SetKeywords("Contratto n. $idC");

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 021', PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('helvetica', '', 9);

        // add a page
        $pdf->AddPage();

        // create some HTML content
        $html = $text;

        // output the HTML content
        $pdf->writeHTML($html, true, 0, true, 0);

        // reset pointer to the last page
        $pdf->lastPage();

        // ---------------------------------------------------------
        \Monkey::app()->router->response()->setContentType('application/pdf');
        $contractText = $pdf->Output('spedizione_.pdf', 'S');
        /** @var CContractsRepo $cR */
        $cR = \Monkey::app()->repoFactory->create('Contracts');
        $contract = $cR->insertTextContract($idC, $contractText);

        //Contract update
        $contract->accepted = 1;
        $nowObject = new \DateTime();
        $now = $nowObject->format('Y-m-d H:i:s');
        $contract->acceptedDate = $now;
        $contract->isActive = 1;
        $contract->update();


        return $contract->id;
    }

}