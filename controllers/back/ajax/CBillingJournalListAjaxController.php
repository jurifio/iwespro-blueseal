<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;

use bamboo\domain\entities\CBillingJournal;

class CBillingJournalListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT  b.id as id, 
                        b.`date` as `date`,  
                        b.totalUeNetReceipt as totalUeNetReceipt,
                        b.totalUeVatReceipt as totalUeVatReceipt,
                        b.totalUeReceipt as totalUeReceipt,
                        b.totalUeNetInvoice as totalUeNetInvoice,
                        b.totalUeVatInvoice as totalUeVatInvoice,
                        b.totalUeInvoice as totalUeInvoice,
                        b.totalXUeNetInvoice as totalXUeNetInvoice,
                        b.totalXUeVatInvoice as totalXUeVatInvoice,
                        b.totalXUeInvoice as totalXUeInvoice,
                        b.groupUeTextReceipt as groupUeTextReceipt,
                        b.groupUeTextInvoice as groupUeTextInvoice,
                        b.groupXUeTextInvoice as groupXUeTextInvoice,
                        b.datePrint as datePrint
                         
                        from BillingJournal b  order By `date` Asc
                    ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);


        foreach ($datatable->getResponseSetData() as $key=>$row) {
            $date = new \DateTime($row['date']);
            $row['date'] = $date->format('d-m-Y');
            $row['totalUeNetReceipt']=money_format('%.2n',  $row['totalUeNetReceipt']) . ' &euro;';
            $row['totalUeVatReceipt']=money_format('%.2n',  $row['totalUeVatReceipt']) . ' &euro;';
            $row['totalUeReceipt']=money_format('%.2n',  $row['totalUeReceipt']) . ' &euro;';
            $row['totalUeNetInvoice']=money_format('%.2n',  $row['totalUeNetInvoice']) . ' &euro;';
            $row['totalUeVatInvoice']=money_format('%.2n',  $row['totalUeVatInvoice']) . ' &euro;';
            $row['totalUeInvoice']=money_format('%.2n',  $row['totalUeInvoice']) . ' &euro;';
            $row['totalXUeNetInvoice']=money_format('%.2n',  $row['totalXUeNetInvoice']) . ' &euro;';
            $row['totalXUeVatInvoice']=money_format('%.2n',  $row['totalXUeVatInvoice']) . ' &euro;';
            $row['totalXUeInvoice']=money_format('%.2n',  $row['totalXUeInvoice']) . ' &euro;';
            if(is_null($row['datePrint'])){
                $row['datePrint']='Da Stampare';
            } else {
                $datePrint=new \DateTime($row['datePrint']);

                $row['datePrint']='<font color="#f0f0f0">'.$datePrint.'</font>';
            }



            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}