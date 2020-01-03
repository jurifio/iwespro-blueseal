<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\export\order\COrderExport;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\repositories\CEmailRepo;

/**
 * Class CDispatchInvoiceToAdministrativeShopContactJobs
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/12/2019
 * @since 1.0
 */
class CDispatchInvoiceToAdministrativeShopContactJobs extends ACronJob
{

    var $success = "ORD_FRND_SENT";
    var $fail = "ORD_ERR_SEND";

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shops = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => 1]);
      $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $today = new \DateTime();
        $dateStart = $today->format('Y-m-d 00:00:00');
        $dateEnd  =  $today->format('Y-m-d 23:59:59');
        $dateSend=$today->format('d-m-Y');
        $dateStart=strtotime($dateStart);
        $dateEnd=strtotime($dateEnd);

        foreach($shops as $shop){
            try {
                $bodyMail = 'Invio Elenco Documenti  Emessi per il giorno ' . $dateSend.'<br>';
                $bodyInvoice = '';
                $bodyList='';
                $bodyLog='';
                $to = [$shop->billingContact];
                $invoices = $invoiceRepo->findBy(['invoiceShopId' => $shop->id]);
                $shopName=$shop->title;
                foreach ($invoices as $invoice) {
                    $dateCheck = strtotime($invoice->invoiceDate);
                    if ($dateCheck >= $dateStart && $dateCheck <= $dateEnd) {
                        $invoiceDate = new \DateTime($invoice->invoiceDate);
                        $dateInvoice=$invoiceDate->format('d-m-Y');
                        $bodyList .= '<b>documento N:' . $invoice->invoiceNumber . '/' . $invoice->invoiceType . ' data: ' . $dateInvoice . '</b><br>';
                        $positionStart=strpos($invoice->invoiceText,'<!--start-->');
                        $positionEnd=strpos($invoice->invoiceText,'<!--end-->');
                        $bodyTextLength=$positionEnd-$positionStart;
                        $bodyInvoiceText=substr($invoice->invoiceText,$positionStart,$bodyTextLength);
                        $headInvoiceText=substr($invoice->invoiceText,0,$positionStart);
                        $footerTextLength=strlen($invoice->invoiceText)-$positionEnd;
                        $footerInvoiceText='</body>
</html>';
                        $bodyInvoice.=$headInvoiceText;
                        $bodyInvoice.=$bodyInvoiceText;
                        $bodyInvoice.=$footerInvoiceText;
                        $bodyInvoice .= '<br>';
                        $bodyLog='documento N:' . $invoice->invoiceNumber . '/' . $invoice->invoiceType . ' data: ' . $dateInvoice . '<br>';
                    }
                }

                /** @var CEmailRepo $emailRepo */
                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                $emailRepo->newPackagedTemplateMail('sendinvoicetoshop','no-reply@iwes.pro',$to,[],[],['shopName'=>$shopName,'bodyList' => $bodyList,'bodyInvoice' => $bodyInvoice,'bodyMail' => $bodyMail]);
                $this->report('CDispatchInvoiceToAdministrativeShopContactJobs', $bodyLog,'');
            }catch(\Throwable $e){
                $this->report('CDispatchInvoiceToAdministrativeShopContactJobs', 'Error',$e);
            }
        }
    }
}