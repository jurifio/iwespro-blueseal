<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class COrderListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CFriendOrderInvoiceListAjaxController extends AAjaxController
{

    public function get()
    {
        $cR = \Monkey::app()->repoFactory->create('Configuration');
        $vat = $cR->findOneBy(['name' => 'main vat'])->value;
        $user = $this->app->getUser();
        $allShops = $user->hasPermission('allShops');
        // Se non è allshop devono essere visualizzate solo le linee relative allo shop e solo a un certo punto di avanzamento

        $query = "
             SELECT
                  `i`.`id` as `id`,
                  `i`.`number` as `invoiceNumber`,
                  `i`.`paymentExpectedDate` as paymentExpectedDate,
                  `i`.`date` as `invoiceDate`,
                  `i`.`totalWithVat` as `invoiceTotalAmount`,
                  `i`.`paymentDate` as `paymentDate`,
                  concat(`ol`.`id`, '-', `ol`.`orderId`) as `orderLines`,
                  `i`.`creationDate` as `creationDate`
                FROM
                  `InvoiceNew` as `i` JOIN
                  `InvoiceLine` as `il` on `il`.`invoiceId` =  `i`.`id` JOIN
                  `InvoiceType` as `it` on `it`.`id` = `i`.`invoiceTypeId` JOIN
                  `InvoiceLineHasOrderLine` as `ilhol` on `il`.`id` = `ilhol`.`invoiceLineId` AND `il`.`invoiceId` = `ilhol`.`invoiceLineInvoiceId` JOIN
                  `OrderLine` as `ol` on `ilhol`.`orderLineOrderId` = `ol`.`orderId` AND `ilhol`.`orderLineId` = `ol`.`id`
                WHERE
                  `it`.`code` = 'fr_invoice_orderlines_file'
              ";

        $datatable = new CDataTables($query, ['id'],$_GET, true);

        $invoices = $this->app->repoFactory->create('InvoiceNew')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('InvoiceNew')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('InvoiceNew')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        $i = 0;

        foreach ($invoices as $v) {
	        /** ciclo le righe */
            $response['data'][$i]['id'] = $v->id;
            $response['data'][$i]['invoiceNumber'] = $v->number;
            $response['data'][$i]['paymentExpectedDate'] = $v->paymentExpectedDate;
            $response['data'][$i]['paymentDate'] = $v->paymentDate;
            $response['data'][$i]['creationDate'] = $v->creationDate;
            $response['data'][$i]['invoiceTotalAmount'] = $v->totalWithVat;
            $response['data'][$i]['invoiceDate'] = $v->date;
            $response['data'][$i]['orderLines'] = '<span>[da implementare]</span>';
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