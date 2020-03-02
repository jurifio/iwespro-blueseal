<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CInvoiceType;
use bamboo\domain\entities\CBillRegistryActivePaymentSlip;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CBillRegistryActivePaymentSlipListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/03/2020
 * @since 1.0
 */
class CBillRegistryActivePaymentSlipListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "pb.id,
                    `pb`.`numberSlip` as `numberSlip`,
                  `pb`.`amount` AS `total`,
                  `pb`.`creationDate` as `creationDate`,
                  `pb`.`paymentDate` as `PaymentDate`,
                  `pb`.`submissionDate` as `submissionDate`,
                  `pb`.`note` as `note`,
                  `brps`.`name` as `statusId`,
                  count(DISTINCT `bri`.`billRegistryClientId`) AS `transfers`,
                  group_concat(DISTINCT   `brc`.`companyName`) AS `recipients`,
                  group_concat(DISTINCT concat(`bri`.`invoiceNumber`,'-', `bri`.`invoiceType`, '-',`bri`.`invoiceYear`)) AS `invoices`
                FROM `BillRegistryActivePaymentSlip` `pb`
                 JOIN `BillRegistryTimeTable` `brtt`  on `pb`.`id` = `brtt`.`billRegistryActivePaymentSlipId` 
                LEFT JOIN `BillRegistryInvoice` `bri` on `brtt`.`billRegistryInvoiceId` =`bri`.`id`
                LEFT JOIN `BillRegistryTypePayment` `brtp` on `bri`.`billRegistryTypePaymentId` = `brtp`.`id`
                LEFT JOIN `BillRegistryClient` `brc` on `bri`.`billRegistryClientId`=`brc`.`id`
                join `BillRegistryPaymentSlipStatus` `brps` on `pb`.`statusId`=`brps`.`id`
                GROUP BY `pb`.`id`";

        $datatable = new CDataTables($sql,['id'],$_GET,true);
        $datatable->doAllTheThings(true);

        $paymentBillRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
        $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryPaymentSlipRepo=\Monkey::app()->repoFactory->create('BillRegistryPaymentSlip');

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CBillRegistryActivePaymentSlip $paymentBill */
            $paymentBill = $paymentBillRepo->findOneBy($row);

            $row["DT_RowId"] = $paymentBill->printId();
            $row["DT_RowClass"] = 'colore';
            $row["id"] = $paymentBill->printId();
            $rec = [];
            $invoiceList='';
            $clientList='';
            $impAmountTot=0;
            $amountSlip=0;
            $typePayment='';
            $bps=$billRegistryPaymentSlipRepo->findOneBy(['id'=>$paymentBill->statusId]);
            $timeTable=$billRegistryTimeTableRepo->findBy(['billRegistryActivePaymentBillId'=>$paymentBill->id]);
            foreach($timeTable as $tt){
                $amountSlip+=$tt->amountPayment;
                $invoice=$billRegistryInvoiceRepo->findOneBy(['id'=>$tt->billRegistryInvoiceId]);
                $typePayment=$billRegistryTypePaymentRepo->findOneBy(['id'=>$invoice->billRegistryTypePaymentId]);
                $client=$billRegistryClientRepo->findOneBy(['id'=>$invoice->billRegistryClientId]);
                $invoiceList.=$invoice->invoiceNumber.'-'.$invoiceType.'-'.$invoice->invoiceYear.'<br>' ;
                $impAmount+=$invoice->grossTotal;
            }


           $row['numberSlip']=$paymentBill->numberSlip;
            $row['recipients']=$client->companyName;
            $row['invoices'] = $invoiceList;
            $row['statusId']=$bps->name;
            $row['creationDate']=STimeToolbox::FormatDateFromDBValue($paymentBill->creationDate,STimeToolbox::ANGLO_DATE_FORMAT);
            $row['paymentDate'] = STimeToolbox::FormatDateFromDBValue($paymentBill->paymentDate,STimeToolbox::ANGLO_DATE_FORMAT);
            $row['submissionDate'] = $paymentBill->submissionDate ?? 'Non Sottomessa';
            $row['impAmount']=$impAmount;
            $row['amountSlip']=$amountSlip;
            $row['typePayment']=$typePayment->name;
            $row['note'] = $paymentBill->note;
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}