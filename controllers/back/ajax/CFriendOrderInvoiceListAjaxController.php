<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CInvoiceLineHasOrderLine;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\utils\price\SPriceToolbox;
use bamboo\utils\time\STimeToolbox;

class CFriendOrderInvoiceListAjaxController extends AAjaxController
{

    public function get()
    {
        $query = "
             SELECT
              `i`.`id`                                                                        AS `id`,
              `i`.`number`                                                                    AS `invoiceNumber`,
              `i`.`paymentExpectedDate`                                                       AS paymentExpectedDate,
              `i`.`date`                                                                      AS `invoiceDate`,
              `i`.`totalWithVat`                                                              AS `invoiceTotalAmount`,
              /*concat(`it`.`name`, 
                if(`it`.`id` = 6,
                  concat(' - NdC: ', 
                    (SELECT DISTINCT `number` FROM `Document` as subD JOIN InvoiceLineHasOrderLine as subIL on subIL.invoiceLineInvoiceId = subD.id 
                      WHERE subIL.orderLineId = `ol`.id AND `subIL`.`orderLineOrderId` = `ol`.`orderId` AND `subD`.`invoiceTypeId` = 4 OR `subD`.`invoiceTypeId` = 5
                    )
                  ), ''
                )
              )as `documentT`,*/
              round((sum(ol.friendRevenue) / 100 * 22) + sum(ol.friendRevenue), 2)            AS invoiceCalculatedTotal,
              `it`.`name`                                                                     AS `documentType`,
              `it`.`id`                                                                       AS `dt`,
              if(`i`.`paymentDate`, DATE_FORMAT(`i`.`paymentDate`, '%d-%m-%Y'), 'Non Pagato') AS `paymentDate`,
              group_concat(concat(`ol`.`id`, '-', `ol`.`orderId`))                            AS `orderLines`,
              `i`.`creationDate`                                                              AS `creationDate`,
              if(`pb`.`id`, group_concat(DISTINCT `pb`.`id`), 'Non presente')                 AS `paymentBill`,
              `sh`.`title`                                                                    AS friend,
              `ab`.`id`                                                                       AS abid,
              sh.id                                                                           AS shopId,
              ifnull(pb.paymentDate, '')                                                      AS paymentBillDate,
              i.note                                                                          AS note
            FROM
              `Document` AS `i`
              JOIN `InvoiceType` AS `it` ON `it`.`id` = `i`.`invoiceTypeId`
              JOIN `AddressBook` AS ab ON `i`.`shopRecipientId` = `ab`.`id`
              JOIN `Shop` AS sh ON `i`.`shopRecipientId` = `sh`.`billingAddressBookId`
              LEFT JOIN `InvoiceLine` AS `il` ON `il`.`invoiceId` = `i`.`id`
              LEFT JOIN `InvoiceLineHasOrderLine` AS `ilhol`
                ON `il`.`id` = `ilhol`.`invoiceLineId` AND `il`.`invoiceId` = `ilhol`.`invoiceLineInvoiceId`
              LEFT JOIN `OrderLine` AS `ol` ON `ilhol`.`orderLineOrderId` = `ol`.`orderId` AND `ilhol`.`orderLineId` = `ol`.`id`
              LEFT JOIN (`PaymentBillHasInvoiceNew` AS `pbhin`
                JOIN `PaymentBill` AS `pb` ON `pb`.id = `pbhin`.`paymentBillId`) ON `i`.`id` = `pbhin`.`invoiceNewId`
            GROUP BY `i`.`id`
              ";


        $datatable = new CDataTables($query, ['id'],$_GET, true);
        $datatable->addCondition('shopId',$this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());


        $datatable->doAllTheThings(true);

        $abR = \Monkey::app()->repoFactory->create('AddressBook');
        /** @var CInvoiceLineHasOrderLine $ilhR */
        $ilhR = \Monkey::app()->repoFactory->create('InvoiceLineHasOrderLine');
        /** @var CDocumentRepo $documentRepo */
        $documentRepo = \Monkey::app()->repoFactory->create('Document');
        foreach ($datatable->getResponseSetData() as $key=>$row) {
	        /** ciclo le righe */
	        $v = $documentRepo->findOneBy($row);
            $row['DT_RowId'] = $v->printId();
            $row['id'] = $v->id;
            $ab = $abR->findOne([$v->shopRecipientId]);
            $friend = ($ab && $ab->shop) ? $ab->shop->title : "Non trovo il friend";
            $row['friend'] = $friend;
            $row['invoiceNumber'] = $v->number;
            $row['paymentExpectedDate'] = STimeToolbox::EurFormattedDate($v->paymentExpectedDate);
            $paymentDate = (null !== $v->paymentDate && '0000-00-00 00:00:00' == $v->paymentDate ) ? 'Non pagata' : STimeToolbox::EurFormattedDate($v->paymentDate);
            $row['paymentDate'] = $paymentDate;
            $row['creationDate'] = STimeToolbox::EurFormattedDate($v->creationDate);
            $row['invoiceTotalAmount'] = SPriceToolbox::formatToEur($v->totalWithVat);
            $invoiceLinesTotal = 0;
            foreach($v->invoiceLine as $il) {
                $invoiceLinesTotal+= $il->price;
            }

            $row['documentType'] = $v->invoiceType->name;
            /*$typeId = $v->invoiceType->id;
            if (6 == $typeId) {
                $ol = $v->orderLine->getFirst();
                $ils = $ilhR->findBy(['orderLineId' => $ol->id, 'orderLineOrderId' => $ol->id]);
                foreach($ils as $il) {
                    if (5 == $il->document->invoiceTypeId OR 4 == $il->document->invoiceTypeId) {
                        $row['documentType'].= ' NdC: ' . $il->document->number;
                    }
                }
            }*/
            $row['invoiceCalculatedTotal'] = SPriceToolbox::formatToEur($invoiceLinesTotal);
            $row['invoiceDate'] = STimeToolbox::EurFormattedDate($v->date);
            $bill = $v->paymentBill;
            $arrBillId = [];
            foreach($bill as $v) {
                $arrBillId[] = $v->id;
            }
            $echoBill = (count($arrBillId)) ? implode(', ', $arrBillId) : 'Non presente';

            $row['paymentBill'] = $echoBill;

            $datatable->setResponseDataSetRow($key,$row);
	    }
        return $datatable->responseOut();
    }

    public function post()
    {
        throw new \Exception();
    }

    public function delete()
    {
        throw new \Exception();
    }
}