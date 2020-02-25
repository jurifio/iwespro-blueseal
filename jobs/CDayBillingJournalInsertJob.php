<?php

namespace bamboo\blueseal\jobs;
use bamboo\core\base\CFTPClient;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooFTPClientException;
use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CDayBillingJournalInsertJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/02/2019
 * @since 1.0
 */
class CDayBillingJournalInsertJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        try {
            /* $sql = 'DELETE FROM `BillingJournal` WHERE userId=' . $id;
             \Monkey::app()->dbAdapter->query($sql,[]);*/
            $shops = \Monkey::app()->repoFactory->create('Shop') - findBy(['hasEcommerce' => 1]);
            foreach ($shops as $shop) {

                $today = new \DateTime();
                $resultdate = $today->format('Y-m-d');
// totale Ricevute
                $sql = "SELECT sum(O.netTotal) AS netTotalReceipt,
              sum(O.vat) AS vatTotalReceipt,
              sum(O.grossTotal)AS grossTotalReceipt ,
              I.invoiceDate as invoiceDate
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                WHERE I.invoiceDate >= '" . $resultdate . " 00:00:00' and I.invoiceDate <= '" . $resultdate . " 23:59:59'   AND I.invoiceType='".$shop->receipt."' ";

                $resultTotalReceipt = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
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
                WHERE I.invoiceDate >= '" . $resultdate . " 00:00:00' and I.invoiceDate <= '" . $resultdate . " 23:59:59'   AND I.invoiceType='".$shop->invoiceUe."' ";

                $resultTotalInvoice = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
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
                WHERE I.invoiceDate >= '" . $resultdate . " 00:00:00' and I.invoiceDate <= '" . $resultdate . " 23:59:59'   AND I.invoiceType='".$shop->invoiceExtraUe."' ";

                $resultTotalXInvoice = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                forEach ($resultTotalXInvoice as $resultTotalXInvoices) {
                    $totalXUeNetXInvoice = $resultTotalXInvoices['netTotalXInvoice'];
                    $totalXUeVatInvoice = $resultTotalXInvoices['vatTotalXInvoice'];
                    $totalXUeInvoice = $resultTotalXInvoices['grossTotalXInvoice'];
                }
                $sql = "SELECT concat(I.invoiceNumber,'/',I.invoiceType) AS groupUeTextReceipt,
                I.invoiceDate as InvoiceDate
        
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                 WHERE I.invoiceDate >= '" . $resultdate . " 00:00:00' and I.invoiceDate <= '" . $resultdate . " 23:59:59'   AND I.invoiceType='".$shop->receipt."' ";
                $groupUeTextReceipt = 'Ricevute UE Emesse Sezionali '.$shop->name.' :<br> ';
                $resultGroupUeTextReceipt = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                forEach ($resultGroupUeTextReceipt as $resultGroupUeTextReceipts) {
                    $groupUeTextReceipt .= $resultGroupUeTextReceipts['groupUeTextReceipt'] . "<br>";
                }

                $sql = "SELECT concat(I.invoiceNumber,'/',I.invoiceType) AS groupUeTextInvoice,
              I.invoiceDate AS invoiceDate
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                 WHERE I.invoiceDate >= '" . $resultdate . " 00:00:00' and I.invoiceDate <= '" . $resultdate . " 23:59:59'   AND I.invoiceType='".$shop->invoiceUe."' ";
                $groupUeTextInvoice = 'Fatture UE Emesse Sezionali '.$shop->name.' :<br> ';
                $resultGroupUeTextInvoice = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                forEach ($resultGroupUeTextInvoice as $resultGroupUeTextInvoices) {
                    $groupUeTextInvoice .= $resultGroupUeTextInvoices['groupUeTextInvoice'] . "<br>";
                }
                $sql = "SELECT concat(I.invoiceNumber,'/',I.invoiceType) AS groupXUeTextInvoice,
            I.invoiceDate as InvoiceDate
              FROM Invoice I 
              JOIN `Order` O ON I.orderId = O.id  
                 WHERE I.invoiceDate >= '" . $resultdate . " 00:00:00' and I.invoiceDate <= '" . $resultdate . " 23:59:59'   AND I.invoiceType='".$shop->invoiceExtraUe."' ";
                $groupXUeTextInvoice = 'Fatture ExtraUE Emesse Sezionali '.$shop->name.' :<br> ';
                $resultGroupXUeTextInvoice = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                forEach ($resultGroupXUeTextInvoice as $resultGroupXUeTextInvoices) {
                    $groupXUeTextInvoice .= $resultGroupXUeTextInvoices['groupXUeTextInvoice'] . "<br>";
                }

                $billingJournalRepo = \Monkey::app()->repoFactory->create('BillingJournal');

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
                $billingJournalInsert->shopId=$shopId;
                $billingJournalInsert->insert();


            }

        } catch (\Throwable $e) {
            $res = $e;

           \Monkey::app()->applicationReport('CDayBillingJournalInsertJob','Insert Day Billing Journal','error',$res);
        }


    }

}