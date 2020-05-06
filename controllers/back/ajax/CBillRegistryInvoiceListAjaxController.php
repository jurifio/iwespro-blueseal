<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;

/**
 * Class CBillRegistryInvoiceListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/02/2020
 * @since 1.0
 */
class CBillRegistryInvoiceListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      `bri`.`id`                                            AS `id`,
                       concat(`bri`.`invoiceNumber`,'/',`bri`.`invoiceType`,'-',`bri`.`invoiceYear`)                                       as `invoiceNumber`,
                      `brc`.`companyName`                                      AS `companyName`,
                      `bri`.`netTotal`                                         AS `netTotal`,
                      `bri`.`vat`             AS `vat`,
                      `bri`.`grossTotal`             AS `grossTotal`,
                      `bri`.`invoiceDate`             AS `invoiceDate`,
                       if(`bri`.`isBilled`=1,'Si','No')           AS sendToLegal, 
                      `brtp`.`name` AS typePayment,
                      `bris`.status as status,
                      `braps`.`numberSlip` as `numberSlip`,
                      `bri`.`subject` as `subject`  
                    FROM `BillRegistryInvoice` `bri`
                      JOIN `BillRegistryClient` `brc` on `bri`.`billRegistryClientId`=`brc`.`id`
                        join `BillRegistryClientBillingInfo` `brcbi` on `bri`.`billRegistryClientId`
                      JOIN `BillRegistryTypePayment` `brtp` on `brcbi`.`billRegistryTypePaymentId`= `brtp`.`id`
                      JOIN `BillRegistryTimeTable` btt on bri.id=btt.billRegistryInvoiceId
                        left join BillRegistryActivePaymentSlip braps on btt.BillRegistryActivePaymentSlipId=braps.id
                     JOIN `BillRegistryInvoiceStatus` bris on bri.statusId=bris.id GROUP BY id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $invoiceEdit = $this->app->baseUrl(false) . "/blueseal/anagrafica/fatture-modifica?id=";
        $billRegistryInvoiceStatusRepo=\Monkey::app()->repoFactory->create('BillRegistryInvoiceStatus');
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $billRegistryClientRepo=\Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryTypePaymentRepo=\Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryTimeTableRepo=\Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        $billRegistryActivePaymentSlipRepo=\Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $billRegistryInvoice = $billRegistryInvoiceRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] = 'row__' . $billRegistryInvoice->printId();
            $row['id'] = $billRegistryInvoice->id;
            $row['sendToLegal']=($billRegistryInvoice->isBilled==1)?'Si':'No';
           $billRegistryClient=$billRegistryClientRepo->findOneBy(['id'=>$billRegistryInvoice->billRegistryClientId]);
            $date=new \DateTime($billRegistryInvoice->invoiceDate);
            $row['invoiceDate']=$date->format('d-m-Y');
            $year=$date->format('Y');
            $row['invoiceNumber']='<a href="'.$invoiceEdit.$billRegistryInvoice->id.'">'.$billRegistryInvoice->invoiceNumber.'/'.$billRegistryInvoice->invoiceType.'-'.$year.'</a>';
           $row['companyName']=$billRegistryClient->companyName;
            $row['netPrice']= number_format(trim($billRegistryInvoice->netTotal),2,',','.').' &euro;';
            $row['vat']=number_format($billRegistryInvoice->vat,2,',','.').' &euro;';
            $row['grossTotal']=number_format($billRegistryInvoice->grossTotal,2,',','.').' &euro;';
            $row['subject']=$billRegistryInvoice->subject;

            $billRegistryTypePayment=$billRegistryTypePaymentRepo->findOneBy(['id'=>$billRegistryInvoice->billRegistryTypePaymentId]);
            $btt=$billRegistryTimeTableRepo->findBy(['billRegistryInvoiceId'=>$billRegistryInvoice->id]);
            $bris=$billRegistryInvoiceStatusRepo->findOneBy(['id'=>$billRegistryInvoice->statusId]);

           $row['status']=$bris->status;
            $rowPayment="";
            foreach($btt as $bt){
                $numberSlip='';
                if($bt->billRegistryActivePaymentSlipId!=null){
                    $braps=$billRegistryActivePaymentSlipRepo->findOneBy(['id'=>$bt->billRegistryActivePaymentSlipId]);
                    $numberSlip='distinta n: '.$braps->numberSlip;
                }
                $dateNow=new \DateTime();
                $dateEstimated=new \dateTime($bt->dateEstimated);
                if($dateNow>$dateEstimated && $bt->amountPaid==0 ){
                    $rowPayment.='<i style="color:red;
                    font-size: 12px;
                    display: inline-block;
                    border: red;
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;">
<b>€'.number_format($bt->amountPayment,2,',','.'). ' '.$dateEstimated->format('d-m-Y') . ' '.$numberSlip.'</b></i></br>';
                }elseif($dateNow>=$dateEstimated && $bt->amountPaid!=0){
                    $rowPayment.='<i style="
                    color:green;
                    font-size: 12px;
                    display: inline-block;
                    border: green;
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;">
<b>€'.number_format($bt->amountPayment,2,',','.'). ' '.$dateEstimated->format('d-m-Y') . ' '.$numberSlip.'</b></i></br>';
                }elseif($dateNow<$dateEstimated && $bt->amountPaid==0){
                    $rowPayment.='<i style="
                    color:orange;
                    font-size: 12px;
                    display: inline-block;
                    border: orange;
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;"><b>€'.number_format($bt->amountPayment,2,',','.'). ' '.$dateEstimated->format('d-m-Y') . ' '.$numberSlip.'</b></i></br>';
                }elseif($dateNow<=$dateEstimated && $bt->amountPaid!=0) {
                    $rowPayment .= '<i style="color:green;
                    font-size: 12px;
                    display: inline-block;
                    border: green;
                    border-style: solid;
                    border-width: 1.2px;
                    padding: 0.1em;
                    margin-top: 0.5em;
                    padding-right: 4px;
                    padding-left: 4px;"><b>€' . number_format($bt->amountPayment,2,',','.') . ' ' . $dateEstimated->format('d-m-Y') . ' '.$numberSlip.'</b></i></br>';
                }

            }

            $row['rowPayment']=$rowPayment;
            $row['typePayment']=$billRegistryTypePayment->name;



            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}