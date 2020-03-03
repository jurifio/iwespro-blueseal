<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;

/**
 * Class CBillRegistryTimeTableListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/02/2020
 * @since 1.0
 */
class CBillRegistryTimeTableListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                        `brtt`.`id`                                            AS `id`,
                        `brtt`.`dateEstimated` as `dateEstimated`,
                        `brtt`.`amountPayment` as `amountPayment`,
                        `brtt`.`description` as description,
                        `brtt`.`datePayment` as `datePayment`,
                       concat(`bri`.`invoiceNumber`,'/',`bri`.`invoiceType`,'-',`bri`.`invoiceYear`)                                       as `invoice`,
                      `brc`.`companyName`                                      AS `companyName`,
                      `bri`.`netTotal`                                         AS `netTotal`,
                      `bri`.`vat`             AS `vat`,
                      `bri`.`grossTotal`             AS `grossTotal`,
                      `bri`.`invoiceDate`             AS `invoiceDate`,
                      `brtp`.`name` as typePayment,  
                      `bris`.status as status , 
                         if(`brtt`.`billRegistryActivePaymentSlipId` is null,'non Presente',brpb.paymentBillId) as paymentSlipId,
                        if(`brpb`.`PaymentBillId` is null,'non Presente',pb.id) as paymentBillId,          
                        if(`brpb`.`PaymentBillId` is null,'non Presente',pb.amount) as amountNegative,
                       (`brtt`.amountPayment-`brtt`.`amountPaid`) as restPaid 
    
                    FROM `BillRegistryTimeTable` `brtt`
                        left join `BillRegistryInvoice` `bri` on `brtt`.`billRegistryInvoiceId`=`bri`.`id`
                        left  join `BillRegistryInvoiceStatus` `bris`on bri.statusId=bris.id
                      JOIN `BillRegistryClient` `brc` on `bri`.`billRegistryClientId`=`brc`.`id`
                        join `BillRegistryClientBillingInfo` `brcbi` on `bri`.`billRegistryClientId`
                      JOIN `BillRegistryTypePayment` `brtp` on `brcbi`.`billRegistryTypePaymentId`= `brtp`.`id`
                      left JOIN `BillRegistryActivePaymentSlip` brpb on brtt.billRegistryActivePaymentSlipId=brpb.id
                        left JOIN `PaymentBill` `pb` on `brpb`.`paymentBillId`=`pb`.`id`
                      ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $timeTableEdit = $this->app->baseUrl(false) . "/blueseal/anagrafica/scadenziario-modifica?id=";
        $billRegistryTimeTableRepo=\Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $billRegistryInvoiceStatusRepo=\Monkey::app()->repoFactory->create('BillRegistryInvoiceStatus');
        $billRegistryClientRepo=\Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryTypePaymentRepo=\Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryActivePaymentSlipRepo=\Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
        $paymentBillRepo=\Monkey::app()->repoFactory->create('PaymentBill');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $billRegistryTimeTable = $billRegistryTimeTableRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] =  $billRegistryTimeTable->printId();
            $row['id'] = '<a href="'.$timeTableEdit.$billRegistryTimeTable->id.'">'.$billRegistryTimeTable->id.'</a>';


            $dateEs= new \DateTime($billRegistryTimeTable->dateEstimated);
            $row['dateEstimated']=$dateEs->format('d-m-Y');
            if($billRegistryTimeTable->datePayment != null) {
                $datePa = new \DateTime($billRegistryTimeTable->datePayment);
                $row['datePayment'] = $datePa->format('d-m-Y');
            }else{
                $row['datePayment']='';
                    
            }

            $row['amountPayment']=money_format('%.2n',$billRegistryTimeTable->amountPayment).' &euro;';
            $row['description']=$billRegistryTimeTable->description;
            $billRegistryInvoice=$billRegistryInvoiceRepo->findOneBy(['id'=>$billRegistryTimeTable->billRegistryInvoiceId]);
            if($billRegistryTimeTable->billRegistryActivePaymentSlipId!=null) {
                $paymentSlip = $billRegistryActivePaymentSlipRepo->findOneBy(['id'=>$billRegistryTimeTable->billRegistryActivePaymentSlipId]);
                $paymentSlipId=$paymentSlip->id;
                if($paymentSlip->paymentBillId!=null) {
                    $paymentBill = $paymentBillRepo->findOneBy(['id' => $paymentSlip->paymentBillId]);
                    $amountNegative = $paymentBill->amount;
                    $paymentBillId = $paymentSlip->paymentBillId;
                }else{
                    $amountNegative=0;
                    $paymentBillId='Non Presente';
                }
            }else{
                $paymentSlipId='non Presente';
                $amountNegative=0;
                $paymentBillId='Non Presente';
            }
            $row['paymentSlipId']=$paymentSlipId;
            $row['paymentBillId']=$paymentBillId;
            $row['amountNegative']=number_format($amountNegative,2,',','.').' &euro;';
            $billRegistryClient=$billRegistryClientRepo->findOneBy(['id'=>$billRegistryInvoice->billRegistryClientId]);
            $date=new \DateTime($billRegistryInvoice->invoiceDate);
            $row['invoiceDate']=$date->format('d-m-Y');
            $year=$date->format('Y');
            $row['amountPaid']=number_format($billRegistryTimeTable->amountPaid,2,',','.').' &euro;';
           $row['invoiceNumber']=$billRegistryInvoice->invoiceNumber.'/'.$billRegistryInvoice->invoiceType.'-'.$year;
           $row['companyName']=$billRegistryClient->companyName;
           $row['netPrice']=number_format($billRegistryInvoice->netTotal,2,',','.').' &euro;';
            $row['vat']=number_format($billRegistryInvoice->vat,2,',','.').' &euro;';
            $row['grossTotal']=number_format($billRegistryInvoice->grossTotal,2,',','.').' &euro;';
            $billRegistryTypePayment=$billRegistryTypePaymentRepo->findOneBy(['id'=>$billRegistryInvoice->billRegistryTypePaymentId]);
            $row['typePayment']=$billRegistryTypePayment->name;
            $row['restPaid']=number_format($billRegistryTimeTable->amountPayment-$billRegistryTimeTable->amountPaid,2,',','.').' &euro;';
            $dateNow=new \DateTime();
            $dateEstimated=new \dateTime($billRegistryTimeTable->dateEstimated);
            if($dateNow>$dateEstimated && $billRegistryTimeTable->amountPaid==0 ){
                $rowPayment='<i style="color:white;
                    font-size: 12px;
                    font-style: normal;
                    display: inline-block;
                    border: red;
                     border-radius: 5px;
                    background-color:red;    
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;">
<b>Scaduto</b></i></br>';
            }elseif($dateNow>=$dateEstimated && $billRegistryTimeTable->amountPaid==$billRegistryTimeTable->amountPayment){
                $rowPayment='<i style="
                    color:white;
                    font-style: normal;
                    font-size: 12px;
                    display: inline-block;
                    border: #4cff00;
                     border-radius: 5px;
                    background-color:#4cff00;                       
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;">
<b></b></i></br>';
            }elseif($dateNow<$dateEstimated && $billRegistryTimeTable->amountPaid==0){
                $rowPayment='<i style="
                    color:white;
                    font-size: 12px;
                    font-style: normal;
                    display: inline-block;
                    border: #ff6c00;
                    background-color:#ff6c00;   
                    border-style: solid;
                   border-radius: 5px;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;"><b>Da Saldare</b></i></br>';
            }elseif($dateNow<=$dateEstimated && $billRegistryTimeTable->amountPaid==$billRegistryTimeTable->amountPayment) {
                $rowPayment = '<i style="
                    color:white;
                    font-style: normal;
                    font-size: 12px;
                    display: inline-block;
                    border: #4cff00;
                    border-radius: 5px;
                    background-color:#4cff00;                       
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;"><b>Saldato</b></i></br>';
            }elseif($dateNow>=$dateEstimated && $billRegistryTimeTable->amountPaid<=$billRegistryTimeTable->amountPayment) {
                $rowPayment = '<i style="
                    color:white;
                    font-style: normal;
                    font-size: 12px;
                    display: inline-block;
                    border: #ff00cc;
                   border-radius: 5px;
                    background-color:#ff00cc;                       
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;"><b>Saldato P.</b></i></br>';
            }elseif($dateNow<=$dateEstimated && $billRegistryTimeTable->amountPaid<=$billRegistryTimeTable->amountPayment) {
                $rowPayment = '<i style="
                    color:white;
                    font-style: normal;
                    font-size: 12px;
                    display: inline-block;
                    border: #ff00cc;
                   border-radius: 5px;
                    background-color:#ff00cc;                       
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;"><b>Saldato P.</b></i></br>';
            }
            $row['status']=$rowPayment;

            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}