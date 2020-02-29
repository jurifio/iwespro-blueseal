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
                      `brtp`.`name` AS typePayment,
                      if(`bri`.`isPaid`=1,'Pagata','Non Pagata')             AS `isPaid`,
                      if(`bri`.`isSent`=1,'inviata','No')             AS `isSent`
                    FROM `BillRegistryInvoice` `bri`
                      JOIN `BillRegistryClient` `brc` on `bri`.`billRegistryClientId`=`brc`.`id`
                        join `BillRegistryClientBillingInfo` `brcbi` on `bri`.`billRegistryClientId`
                      JOIN `BillRegistryTypePayment` `brtp` on `brcbi`.`billRegistryTypePaymentId`= `brtp`.`id`
                      JOIN `BillRegistryTimeTable` btt on bri.id=btt.billRegistryInvoiceId GROUP BY id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $invoiceEdit = $this->app->baseUrl(false) . "/blueseal/anagrafica/fatture-modifica?id=";

        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $billRegistryClientRepo=\Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryTypePaymentRepo=\Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryTimeTableRepo=\Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            $billRegistryInvoice = $billRegistryInvoiceRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] = 'row__' . $billRegistryInvoice->printId();
            $row['id'] = $billRegistryInvoice->id;
           $billRegistryClient=$billRegistryClientRepo->findOneBy(['id'=>$billRegistryInvoice->billRegistryClientId]);
            $date=new \DateTime($billRegistryInvoice->invoiceDate);
            $row['invoiceDate']=$date->format('d-m-Y');
            $year=$date->format('Y');
            $row['invoiceNumber']='<a href="'.$invoiceEdit.$billRegistryInvoice->id.'">'.$billRegistryInvoice->invoiceNumber.'/'.$billRegistryInvoice->invoiceType.'-'.$year.'</a>';
           $row['companyName']=$billRegistryClient->companyName;
           $row['netPrice']=money_format('%.2n',$billRegistryInvoice->netTotal).' &euro;';
            $row['vat']=money_format('%.2n',$billRegistryInvoice->vat).' &euro;';
            $row['grossTotal']=money_format('%.2n',$billRegistryInvoice->grossTotal).' &euro;';
            $billRegistryTypePayment=$billRegistryTypePaymentRepo->findOneBY(['id'=>$billRegistryInvoice->billRegistryTypePaymentId]);
            $btt=$billRegistryTimeTableRepo->findBy(['billRegistryInvoiceId'=>$billRegistryInvoice->id]);
            $rowPayment="";

            foreach($btt as $bt){
                $dateNow=new \DateTime();
                $dateEstimated=new \dateTime($bt->dateEstimated);
                if($dateNow>$dateEstimated && $bt->amountPaid==0 ){
                    $rowPayment.='<i style="color:red"><b>€'.number_format($btt->amountPayment,2,',','.'). ' '.$dateEstimated->format('d-m-Y'). ' </b><i></br>';
                }elseif($dateNow>=$dateEstimated && $BT->amountPaid!=0){
                    $rowPayment.='<i style="color:green"><b>€'.number_format($btt->amountPayment,2,',','.'). ' '.$dateEstimated->format('d-m-Y'). ' </b><i></br>';
                }elseif($dateNow<$dateEstimated && $BT->amountPaid==0){
                    $rowPayment.='<i style="color:orange"><b>€'.number_format($btt->amountPayment,2,',','.'). ' '.$dateEstimated->format('d-m-Y'). ' </b><i></br>';
                }elseif($dateNow<=$dateEstimated && $BT->amountPaid!=0) {
                    $rowPayment .= '<i style="color:green"><b>€' . number_format($btt->amountPayment,2,',','.') . ' ' . $dateEstimated->format('d-m-Y') . ' </b><i></br>';
                }

            }
            $row['rowPayment']=$rowPayment;
            $row['typePayment']=$billRegistryTypePayment->name;
            if($billRegistryInvoice->isPaid==1){
                $row['isPaid']='Si';
            }else{
                $row['isPaid']='No';
            }
            if($billRegistryInvoice->isSent==1){
                $row['isSent']='Si';
            }else{
                $row['isSent']='No';
            }


            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}