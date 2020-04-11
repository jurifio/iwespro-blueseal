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
        $sql = "select `braps`.`id`,
                    `braps`.`numberSlip` as `numberSlip`,
                  `braps`.`amount` AS `total`,
                  `braps`.`creationDate` as `creationDate`,
                  `braps`.`paymentDate` as `PaymentDate`,
                  `braps`.`submissionDate` as `submissionDate`,
                  `braps`.`note` as `note`,
                  `brps`.`name` as `statusId`,
                  `brca`.`shopId` as shopId, 
                  `pb`.id as paymentBillId,
                  `pb`.amount as negativeAmount,  
                  `braps`.`amount` as positiveAmount,  
                  count(DISTINCT `bri`.`billRegistryClientId`) AS `transfers`,
                  group_concat(DISTINCT   `brc`.`companyName`) AS `companyName`,
                  group_concat(DISTINCT concat(`bri`.`invoiceNumber`,'-', `bri`.`invoiceType`, '-',`bri`.`invoiceYear`)) AS `invoices`
                FROM `BillRegistryActivePaymentSlip` `braps`
                 JOIN `BillRegistryTimeTable` `brtt`  on `braps`.`id` = `brtt`.`billRegistryActivePaymentSlipId` 
                LEFT JOIN `BillRegistryInvoice` `bri` on `brtt`.`billRegistryInvoiceId` =`bri`.`id`      
                 LEFT JOIN `BillRegistryClientAccount` `brca` on `bri`.`id`=`brca`.`billRegistryClientId`   
                LEFT JOIN `BillRegistryTypePayment` `brtp` on `bri`.`billRegistryTypePaymentId` = `brtp`.`id`
                LEFT JOIN `BillRegistryClient` `brc` on `bri`.`billRegistryClientId`=`brc`.`id`
                join `BillRegistryActivePaymentSlipStatus` `brps` on `braps`.`statusId`=`brps`.`id`
                LEFT JOIN PaymentBill pb on braps.paymentBillId=pb.id
                LEFT JOIN Document d on pb.id=d.id      
                LEFT JOIN AddressBook a on d.shopRecipientId=a.id


                GROUP BY `braps`.`id`";

        $datatable = new CDataTables($sql,['id'],$_GET,true);
        $datatable->doAllTheThings(true);

        $paymentBillRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
        $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryPaymentSlipRepo=\Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlipStatus');
        $billRegistryClientAccountRepo=\Monkey::app()->repoFactory->create('BillRegistryClientAccount');
        $pbRepo=\Monkey::app()->repoFactory->create('PaymentBill');
        $documentRepo=\Monkey::app()->repoFactory->create('BillRegistryClientAccount');
        $userAddressRepo=\Monkey::app()->repoFactory->create('UserAddress');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CBillRegistryActivePaymentSlip $paymentBill */
            $paymentBill = $paymentBillRepo->findOneBy($row);

            $row["DT_RowId"] = $paymentBill->printId();
            $row["DT_RowClass"] = 'colore';
            $row["id"] = $paymentBill->printId();
            $rec = [];
            $invoiceList='';
            $clientList='';
            $impAmount=0;
            $amountSlip=0;
            $typePayment='';
            $bps=$billRegistryPaymentSlipRepo->findOneBy(['id'=>$paymentBill->statusId]);
            $timeTable=$billRegistryTimeTableRepo->findBy(['billRegistryActivePaymentSlipId'=>$paymentBill->id]);
            foreach($timeTable as $tt){
                $amountSlip+=$tt->amountPayment;
                $invoice=$billRegistryInvoiceRepo->findOneBy(['id'=>$tt->billRegistryInvoiceId]);
                $typePayment=$billRegistryTypePaymentRepo->findOneBy(['id'=>$invoice->billRegistryTypePaymentId]);
                $client=$billRegistryClientRepo->findOneBy(['id'=>$invoice->billRegistryClientId]);
                $clientAccount=$billRegistryClientAccountRepo->findOneBy(['billRegistryClientId'=>$client->id]);
                if($clientAccount!=null){
                    $shopIdFind=$clientAccount->shopId;
                    $shop=$shopRepo->findOneBy(['id'=>$shopIdFind]);
                    $shopId=$shop->billingAddressBookId;
                }else{
                    $shopId='';
                }
                $invoiceList.=$invoice->invoiceNumber.'-'.$invoice->invoiceType.'-'.$invoice->invoiceYear.'('.number_format($invoice->grossTotal,2,',','.').')<br>' ;
                $impAmount+=$invoice->grossTotal;
            }
            $pb=$pbRepo->findOneBy(['id'=>$paymentBill->paymentBillId]);
            if($pb!=null){
                $numSlipPass=$pb->id;
                $negativeAmount=$pb->amount;
            }else{
                $numSlipPass='';
                $negativeAmount=0;
            }

            $row['id']=$paymentBill->id;
            $row['numberSlip']=$paymentBill->numberSlip;
            $row['companyName']=$client->companyName;
            $row['invoices'] = $invoiceList;
            $row['statusId']=$bps->name;
            $row['paymentBillId']=$numSlipPass;
            $row['creationDate']=STimeToolbox::FormatDateFromDBValue($paymentBill->creationDate,STimeToolbox::ANGLO_DATE_FORMAT);
            $row['paymentDate'] = STimeToolbox::FormatDateFromDBValue($paymentBill->paymentDate,STimeToolbox::ANGLO_DATE_FORMAT);
            $row['submissionDate'] = $paymentBill->submissionDate ?? 'Non Sottomessa';
            $row['impAmount']=number_format($impAmount,'2',',','.').' &euro;';
            $row['impSlip']=number_format($amountSlip,'2',',','.').' &euro;';
            $row['typePayment']=$typePayment->name;
            $row['impPassive']=number_format($negativeAmount,'2',',','.').' &euro;';
            $row['negativeAmount']=number_format($paymentBill->amount-$negativeAmount,'2',',','.').' &euro;';
            $row['note'] = $paymentBill->note;
            $row["DT_RowShopId"]=$shopId;
            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}