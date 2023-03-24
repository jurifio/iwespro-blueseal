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
                        s.name as shopName,
                        s.id as shopId,
                        b.datePrint as datePrint
                        from BillingJournal b join Shop s on b.shopId=s.id order By `date` Asc
                    ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        foreach ($datatable->getResponseSetData() as $key=>$row) {
            $date = new \DateTime($row['date']);
            $row['date'] = $date->format('d-m-Y');
            $row['totalUeNetReceipt']=number_format(  $row['totalUeNetReceipt']) . ' &euro;';
            $row['totalUeVatReceipt']=number_format(  $row['totalUeVatReceipt']) . ' &euro;';
            $row['totalUeReceipt']=number_format(  $row['totalUeReceipt']) . ' &euro;';
            $row['totalUeNetInvoice']=number_format(  $row['totalUeNetInvoice']) . ' &euro;';
            $row['totalUeVatInvoice']=number_format(  $row['totalUeVatInvoice']) . ' &euro;';
            $row['totalUeInvoice']=number_format(  $row['totalUeInvoice']) . ' &euro;';
            $row['totalXUeNetInvoice']=number_format(  $row['totalXUeNetInvoice']) . ' &euro;';
            $row['totalXUeVatInvoice']=number_format(  $row['totalXUeVatInvoice']) . ' &euro;';
            $row['totalXUeInvoice']=number_format(  $row['totalXUeInvoice']) . ' &euro;';

            if(is_null($row['datePrint'])){
                $row['datePrint']='Da Stampare';
            }



            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}