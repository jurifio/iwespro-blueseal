<?php

namespace bamboo\controllers\back\ajax;

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
        $sql = "SELECT pb.id,
                  pb.amount AS total,
                  pb.creationDate,
                  pb.paymentDate,
                  pb.submissionDate,
                  pb.note,
                  count(DISTINCT inn.shopRecipientId) AS transfers,
                  group_concat(DISTINCT s.title) AS recipients,
                  group_concat(DISTINCT inn.number) AS invoices,
                  group_concat(DISTINCT ab.subject)
                FROM PaymentBill pb
                  LEFT JOIN (PaymentBillHasInvoiceNew pbhin 
                      JOIN Document inn ON pbhin.invoiceNewId = inn.id
                      JOIN Shop s ON inn.shopRecipientId = s.billingAddressBookId
                      JOIN InvoiceType it ON inn.invoiceTypeId = it.id
                      JOIN AddressBook ab ON inn.shopRecipientId = ab.id)
                  ON pb.id = pbhin.paymentBillId
                GROUP BY pb.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);
        $datatable->doAllTheThings(true);

        $paymentBillRepo = $this->app->repoFactory->create('PaymentBill');

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CPaymentBill $paymentBill */
            $paymentBill = $paymentBillRepo->findOneBy($row);

            $row["DT_RowId"] = $paymentBill->printId();
            $row["DT_RowClass"] = 'colore';
            $row["id"] = $paymentBill->printId();
            $rec = [];
            foreach ($paymentBill->getDistinctPayments() as $payment) {
                $name = $payment[0]->shopAddressBook->subject;
                $total = 0;
                foreach ($payment as $invoice) {
                    $total += $invoice->getSignedValueWithVat(true);
                }
                $rec[] = $name . ': ' . round($total, 2);

            }
            $row['total'] = $paymentBill->getTotal();
            $row['recipients'] = implode('<br />', $rec);

            $inv = [];
            foreach ($paymentBill->document as $invoice) {
                try {
                    if ($invoice->getSignedValueWithVat() < 0) $color = "text-green";
                    elseif ($invoice->getSignedValueWithVat(true) != $invoice->calculateOurTotal()) $color = "text-red";
                    else $color = "";
                    $inv[] = '<span class="' . $color . '">' . $invoice->shopAddressBook->shop->name . ' - ' . $invoice->number . ': ' . $invoice->getSignedValueWithVat() . ' (' . $invoice->calculateOurTotal() . ')</span>';
                } catch (\Throwable $e) {
                    \Monkey::dump($e->getMessage());
                }

            }
            $row['invoices'] = implode('<br />', $inv);
            $row['paymentDate'] = STimeToolbox::FormatDateFromDBValue($paymentBill->paymentDate, STimeToolbox::ANGLO_DATE_FORMAT);
            $row['creationDate'] = $paymentBill->creationDate;
            $row['submissionDate'] = $paymentBill->submissionDate ?? 'Non Sottomessa';
            $row['note'] = $paymentBill->note;
            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}