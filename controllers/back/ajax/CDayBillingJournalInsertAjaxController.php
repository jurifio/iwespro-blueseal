<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;

use PDO;
use prepare;

use bamboo\core\exceptions\BambooConfigException;
use bamboo\core\base\CObjectCollection;
use bamboo\utils\time\STimeToolbox;


/**
 * Class CDayBillingJournalInsertAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/02/2019
 * @since 1.0
 *
 */
class CDayBillingJournalInsertAjaxController extends AAjaxController
{


    /**
     * @return string
     *
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {
        try {
            $today = new \DateTime();
            $resultdate = $today->format('Y-m-d');
// totale Ricevute
            $sql = "SELECT sum(O.netTotal) AS netTotalReceipt,
              sum(O.vat) AS vatTotalReceipt,
              sum(O.grossTotal)AS grossTotalReceipt ,
              I.invoiceDate as invoiceDate
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                WHERE invoiceDate between '" . $resultdate . " 00:00:00' and '" . $resultdate . " 23:59:59'  AND I.invoiceType='K' ";

            $resultTotalReceipt = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
            forEach ($resultTotalReceipt as $resultTotalReceipts) {
                $totalUeNetReceipt = $resultTotalReceipts['netTotalReceipt'];
                $totalUeVatReceipt = $resultTotalReceipts['vatTotalReceipt'];
                $totalUeReceipt = $resultTotalReceipts['grossTotalReceipt'];
            }
            $sql = "SELECT sum(O.netTotal) AS netTotalInvoice,
              sum(O.vat) AS vatTotalInvoice,
              sum(O.grossTotal)AS grossTotalInvoice, 
              I.invoiceDate as invoiceDate
              FROM Invoice I 
             JOIN `Order` O ON I.orderId = O.id  
                WHERE invoiceDate between '" . $resultdate . " 00:00:00' and '" . $resultdate . " 23:59:59' AND I.invoiceType='P' ";

            $resultTotalInvoice = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
            forEach ($resultTotalInvoice as $resultTotalInvoices) {
                $totalUeNetInvoice = $resultTotalInvoices['netTotalInvoice'];
                $totalUeVatInvoice = $resultTotalInvoices['vatTotalInvoice'];
                $totalUeInvoice = $resultTotalInvoices['grossTotalInvoice'];
            }
            $sql = "SELECT sum(O.netTotal) AS netTotalXInvoice,
              sum(O.vat) AS vatTotalXInvoice,
              sum(O.grossTotal)AS grossTotalXInvoice,
              I.invoiceDate as InvoiceDate
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                WHERE invoiceDate between '" . $resultdate . " 00:00:00' and '" . $resultdate . " 23:59:59' AND I.invoiceType='X' ";

            $resultTotalXInvoice = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
            forEach ($resultTotalXInvoice as $resultTotalXInvoices) {
                $totalXUeNetXInvoice = $resultTotalXInvoices['netTotalXInvoice'];
                $totalXUeVatInvoice = $resultTotalXInvoices['vatTotalXInvoice'];
                $totalXUeInvoice = $resultTotalXInvoices['grossTotalXInvoice'];
            }
            $sql = "SELECT concat(I.invoiceNumber,'/',I.invoiceType) AS groupUeTextReceipt,
                I.invoiceDate as InvoiceDate
        
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                WHERE invoiceDate between '" . $resultdate . " 00:00:00' and '" . $resultdate . " 23:59:59' AND I.invoiceType='K' ";
            $groupUeTextReceipt = 'Ricevute UE Emesse Sezionali:<br> ';
            $resultGroupUeTextReceipt = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
            forEach ($resultGroupUeTextReceipt as $resultGroupUeTextReceipts) {
                $groupUeTextReceipt .= $resultGroupUeTextReceipts['groupUeTextReceipt'] . "<br>";
            }

            $sql = "SELECT concat(I.invoiceNumber,'/',I.invoiceType) AS groupUeTextInvoice,
              I.invoiceDate AS invoiceDate
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                WHERE invoiceDate between '" . $resultdate . " 00:00:00' and '" . $resultdate . " 23:59:59' AND I.invoiceType='P' ";
            $groupUeTextInvoice = 'Fatture UE Emesse Sezionali:<br>';
            $resultGroupUeTextInvoice = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
            forEach ($resultGroupUeTextInvoice as $resultGroupUeTextInvoices) {
                $groupUeTextInvoice .= $resultGroupUeTextInvoices['groupUeTextInvoice'] . "<br>";
            }
            $sql = "SELECT concat(I.invoiceNumber,'/',I.invoiceType) AS groupXUeTextInvoice,
            I.invoiceDate as InvoiceDate
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                WHERE invoiceDate between '" . $resultdate . " 00:00:00' and '" . $resultdate . " 23:59:59' AND I.invoiceType='X' ";
            $groupXUeTextInvoice = 'Fatture ExtraUE Emesse Sezionali:<br>';
            $resultGroupXUeTextInvoice = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
            forEach ($resultGroupXUeTextInvoice as $resultGroupXUeTextInvoices) {
                $groupXUeTextInvoice .= $resultGroupXUeTextInvoices['groupXUeTextInvoice'] . "<br>";
            }
            $sql="select count(*) as totalRecord from BillingJournal 
             WHERE date between '" . $resultdate . " 00:00:00' and '" . $resultdate . " 23:59:59'";
            $checkBillingResultExist=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($checkBillingResultExist as $checkBillingResultExists){
            $existRecord=$checkBillingResultExists['totalRecord'];
            }
            $billingJournalRepo = \Monkey::app()->repoFactory->create('BillingJournal');
            if($existRecord==0){

                /** var CRepo $billingJournalRepo */

                $billingJournalInsert = $billingJournalRepo->getEmptyEntity();
                $billingJournalInsert->date = $resultdate;
                $billingJournalInsert->totalUeNetReceipt = $totalUeNetReceipt;
                $billingJournalInsert->totalUeVatReceipt = $totalUeVatReceipt;
                $billingJournalInsert->totalUeReceipt = $totalUeReceipt;
                $billingJournalInsert->totalUeNetInvoice = $totalUeNetInvoice;
                $billingJournalInsert->totalUeVatInvoice = $totalUeVatInvoice;
                $billingJournalInsert->totalUeInvoice = $totalUeInvoice;
                $billingJournalInsert->totalXUeNetInvoice = $totalXUeNetXInvoice;
                $billingJournalInsert->totalXUeVatInvoice = $totalXUeVatInvoice;
                $billingJournalInsert->totalXUeInvoice = $totalXUeInvoice;
                $billingJournalInsert->groupUeTextReceipt = $groupUeTextReceipt;
                $billingJournalInsert->groupUeTextInvoice = $groupUeTextInvoice;
                $billingJournalInsert->groupXUeTextInvoice = $groupXUeTextInvoice;
                $billingJournalInsert->insert();
            }            $res=" Registrazione Registro Incassi eseguita";
            return $res;
        } catch (\Throwable $e) {
            $res=$e;
            return $res;
        }
    }
}






