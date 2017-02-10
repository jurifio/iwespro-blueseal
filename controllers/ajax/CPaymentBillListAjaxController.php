<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\entities\CProduct;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CPaymentBillListAjaxController
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
class CPaymentBillListAjaxController extends AAjaxController
{
    public function get()
    {
        $response = [];

        $sql = "SELECT pb.id, 
                       pb.amount, 
                       pb.creationDate, 
                       pb.paymentDate, 
                       pb.submissionDate, 
                       pb.note,
                       count(distinct inn.shopRecipientId) as transfers,
                       group_concat(inn.number) as invoices,
                       group_concat(ab.subject)
                from PaymentBill pb 
                      JOIN PaymentBillHasInvoiceNew pbhin on pb.id = pbhin.paymentBillId 
                      JOIN InvoiceNew inn on pbhin.invoiceNewId = inn.id
                      JOIN InvoiceType it on inn.invoiceTypeId = it.id
                      JOIN AddressBook ab on shopRecipientId = ab.id
                  GROUP BY pb.id";

        $datatable = new CDataTables($sql, ['id'], $_GET,true);

        $time = microtime(true);
        $paymentBills = $this->app->repoFactory->create('PaymentBill')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $response['queryTime'] = microtime(true) - $time;

        $time = microtime(true);
        $count = $this->app->repoFactory->create('PaymentBill')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $response['countTime'] = microtime(true) - $time;

        $time = microtime(true);
        $totalCount = $this->app->repoFactory->create('PaymentBill')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $response['fullCountTime'] = microtime(true) - $time;


        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $time = microtime(true);
        /** @var CPaymentBill $paymentBill */
        foreach ($paymentBills as $paymentBill) {
            $row = [];

            $row["DT_RowId"] = $paymentBill->printId();
            $row["DT_RowClass"] = 'colore';
            $row["id"] = $paymentBill->printId();
            $rec = [];
            foreach ($paymentBill->getDistinctPayments() as $payment) {
                $name = $payment[0]->shopAddressBook->subject;
                $total = 0;
                foreach ($payment as $invoice) {
                    $total+=$invoice->getSignedValueWithVat(true);
                }
                $rec[] = $name.': '.$total;

            }
            $row['total'] = $paymentBill->getTotal();
            $row['recipients'] = implode('<br />',$rec);

            $inv = [];
            foreach ($paymentBill->invoiceNew as $invoice) {
                if($invoice->getSignedValueWithVat() < 0) $color = "text-green";
                elseif($invoice->getSignedValueWithVat(true) != $invoice->calculateOurTotal()) $color = "text-red";
                else $color = "";
                $inv[] = '<span class="'.$color.'">'.$invoice->shopAddressBook->shop->name.' - '.$invoice->number.': '.$invoice->getSignedValueWithVat().' ('.$invoice->calculateOurTotal().')</span>';
            }
            $row['invoices'] = implode('<br />',$inv);
            $row['paymentDate'] = STimeToolbox::FormatDateFromDBValue($paymentBill->paymentDate,STimeToolbox::ANGLO_DATE_FORMAT);
            $row['creationDate'] = $paymentBill->creationDate;
            $row['submissionDate'] = $paymentBill->submissionDate ?? 'Non Sottomessa';
            $row['note'] = $paymentBill->note;
            $response ['data'][] = $row;
        }
        $response['resTime'] = microtime(true) - $time;
        return json_encode($response);
    }
}