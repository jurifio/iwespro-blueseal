<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\utils\price\SPriceToolbox;
use bamboo\utils\time\STimeToolbox;

class CFriendOrderInvoiceListAjaxController extends AAjaxController
{

    public function get()
    {
        // Se non è allshop devono essere visualizzate solo le linee relative allo shop e solo a un certo punto di avanzamento

        $query = "
             SELECT
                  `i`.`id` as `id`,
                  `i`.`number` as `invoiceNumber`,
                  `i`.`paymentExpectedDate` as paymentExpectedDate,
                  `i`.`date` as `invoiceDate`,
                  `i`.`totalWithVat` as `invoiceTotalAmount`,
                  if (`it`.`code` LIKE '%invoice%', 'Fattura', 'Nota di credito') as `documentType`,
                  if(`i`.`paymentDate`, DATE_FORMAT(`i`.`paymentDate`, '%d-%m-%Y'), 'Non Pagato') as `paymentDate`,
                  concat(`ol`.`id`, '-', `ol`.`orderId`) as `orderLines`,
                  `i`.`creationDate` as `creationDate`,
                  if (`pb`.`id`, group_concat(DISTINCT `pb`.`id`), 'Non presente')  as `paymentBill`,
                  `sh`.`title` as friend,
                  `ab`.`id` as abid,
                  sh.id as shopId
                FROM
                  `Document` as `i`
                  JOIN `InvoiceType` as `it` on `it`.`id` = `i`.`invoiceTypeId`
                  JOIN `AddressBook` as ab on `i`.`shopRecipientId` = `ab`.`id`
                  JOIN `Shop` as sh on `i`.`shopRecipientId` = `sh`.`billingAddressBookId`
                  LEFT JOIN `InvoiceLine` as `il` on `il`.`invoiceId` =  `i`.`id`
                  LEFT JOIN `InvoiceLineHasOrderLine` as `ilhol` on `il`.`id` = `ilhol`.`invoiceLineId` AND `il`.`invoiceId` = `ilhol`.`invoiceLineInvoiceId`
                  LEFT JOIN `OrderLine` as `ol` on `ilhol`.`orderLineOrderId` = `ol`.`orderId` AND `ilhol`.`orderLineId` = `ol`.`id`
                  LEFT JOIN (`PaymentBillHasInvoiceNew` as `pbhin` JOIN `PaymentBill` as `pb` on `pb`.id = `pbhin`.`paymentBillId`) on `i`.`id` = `pbhin`.`invoiceNewId`
                WHERE
                  `it`.`code` like 'fr_%'
                  group by `i`.`id`
              ";

        $datatable = new CDataTables($query, ['id'],$_GET, true);
        $datatable->addCondition('shopId',$this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        $invoices = $this->app->repoFactory->create('Document')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('Document')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Document')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        $i = 0;

        $abR = \Monkey::app()->repoFactory->create('AddressBook');
        foreach ($invoices as $v) {
	        /** ciclo le righe */
            $response['data'][$i]['id'] = $v->id;
            $ab = $abR->findOne([$v->shopRecipientId]);
            $friend = ($ab && $ab->shop) ? $ab->shop->title : "Non trovo il friend";
            $response['data'][$i]['friend'] = $friend;
            $response['data'][$i]['invoiceNumber'] = $v->number;
            $response['data'][$i]['paymentExpectedDate'] = STimeToolbox::EurFormattedDate($v->paymentExpectedDate);
            $paymentDate = (null !== $v->paymentDate && '0000-00-00 00:00:00' == $v->paymentDate ) ? 'Non pagata' : STimeToolbox::EurFormattedDate($v->paymentDate);
            $response['data'][$i]['paymentDate'] = $paymentDate;
            $response['data'][$i]['creationDate'] = STimeToolbox::EurFormattedDate($v->creationDate);
            $response['data'][$i]['invoiceTotalAmount'] = SPriceToolbox::formatToEur($v->totalWithVat);
            $invoiceLinesTotal = 0;
            foreach($v->invoiceLine as $il) {
                $invoiceLinesTotal+= $il->price;
            }

            $response['data'][$i]['documentType'] = (false !== strpos($v->invoiceType->code, 'invoice')) ? 'Fattura' : 'Nota di Credito';
            $response['data'][$i]['invoiceCalculatedTotal'] = SPriceToolbox::formatToEur($invoiceLinesTotal);
            $response['data'][$i]['invoiceDate'] = STimeToolbox::EurFormattedDate($v->date);
            $bill = $v->paymentBill;
            $arrBillId = [];
            foreach($bill as $v) {
                $arrBillId[] = $v->id;
            }
            $echoBill = (count($arrBillId)) ? implode(', ', $arrBillId) : 'Non presente';

            $response['data'][$i]['paymentBill'] = $echoBill;
            $i++;
	    }
        return json_encode($response);
    }

    public function post()
    {
        throw new \Exception();
    }

    public function delete()
    {
        throw new \Exception();
    }

    public function orderBy(){
        $dtOrderingColumns = $_GET['order'];
        $dbOrderingColumns = [
            ['column'=>'o.id'],
            ['column'=>'o.creationDate'],
            ['column'=>'o.lastUpdate']
        ];
        $dbOrderingDefault = [
            ['column'=>'o.creationDate','dir'=>'desc']
        ];

        $sqlOrder = " ORDER BY ";
        foreach ($dtOrderingColumns as $column) {
            if (isset($dbOrderingColumns[$column['column']]) && $dbOrderingColumns[$column['column']]['column'] !== null) {
                $sqlOrder .= $dbOrderingColumns[$column['column']]['column']." ".$column['dir'].", ";
            }
        }
        if (substr($sqlOrder,-1,2) != ', ') {
            foreach($dbOrderingDefault as $column) {
                $sqlOrder .= $column['column'].' '.$column['dir'].', ';
            }
        }
        return rtrim($sqlOrder,', ');
    }
}