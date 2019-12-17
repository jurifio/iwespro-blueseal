<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\utils\price\SPriceToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CFriendOrderWorkingListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/10/2019
 * @since 1.0
 */
class CFriendOrderWorkingListAjaxController extends AAjaxController
{

    public function get()
    {
        $olfpsR = \Monkey::app()->repoFactory->create('OrderLineFriendPaymentStatus');
        /** @var COrderLineRepo $olR */
        $olR = \Monkey::app()->repoFactory->create('OrderLine');
        $cR = \Monkey::app()->repoFactory->create('Configuration');
        $vat = $cR->findOneBy(['name' => 'main vat'])->value;
        $user = $this->app->getUser();
        $allShops = $user->hasPermission('allShops');
        // Se non è allshop devono essere visualizzate solo le linee relative allo shop e solo a un certo punto di avanzamento
        $currentUser=$this->app->getUser();
        $userHasShopRepo=\Monkey::app()->repoFactory->create('userHasShop');
        $userHasShop=$userHasShopRepo->findOneBy(['userId'=>$currentUser]);
        $filterSql= ' ';
        if(!$allShops) {
            if ($userHasShop != null) {
                $filterSql = ' and o.remoteShopSellerId = 44 ';
            }
        }
        $DDTAndNoCreditNote = \Monkey::app()->router->request()->getRequestData('ddtWithoutNcd');
        $DDfield = '';
        $DDThaving = '';
        if($DDTAndNoCreditNote) {
            $DDfield = 'group_concat(`it`.`code`)                                 AS `groupDocumentType`,';
            $DDThaving = " GROUP BY `ol`.`id`, `ol`.`orderId` HAVING groupDocumentType LIKE '%fr_trans_doc%' AND groupDocumentType NOT LIKE '%fr_credit_note%'";
        }

        $query = "
               SELECT
                  `ol`.`id`                                                     AS `id`,
                  `ol`.`orderId`                                                AS `orderId`,
                  `o`.`orderDate`                                               AS `orderDate`,
                  concat(`ol`.`id`, '-', `ol`.`orderId`)                        AS `orderCode`,
                  concat(`p`.`id`, '-', `p`.`productVariantId`, '-', `ps`.`id`) AS `code`,
                  concat(`p`.`itemno`, ' # ', `pv`.`name`)                      AS `cpf`,
                  #l.eventValue as logVal,
                  #l.time as logTime,
                  `pb`.`name`                                                   AS `brand`,
                  concat(
                  if(`it`.`code` like '%fr_invoice%', `in`.`number`, '-'),
                       ',', if(`it`.`code` like '%credito_note%', `in`.`number`, '-'),
                       ',',
                       if(`it`.`code` like '%fr_trans_doc%', `in`.`number`, '-')
                  ) as invoiceAll,
                  if(`it`.`code` like '%fr_invoice%', `in`.`number`, '-') AS `invoiceNumber`,
                  if(`it`.`code` like '%fr_credit_note%', `in`.`number`, '-') AS `creditNoteNumber`,
                  if(`it`.`code` like '%fr_trans_doc%', `in`.`number`, '-') AS `transDocNumber`,
                  `it`.`code` as `DocumentType`,
                  $DDfield
                  `pse`.`name`                                                  AS `season`,
                  `ps`.`name`                                                   AS `size`,
                  `s`.`id`                                                      AS `shopId`,
                  `s`.`title`                                                   AS `shopName`,
                  `os`.`title`                                                  AS `orderStatusTitle`,
                  `o`.`status`                                                  AS `orderStatusCode`,
                  `ol`.status                                                   AS `orderLineStatusCode`,
                  `ols`.title                                                   AS `orderLineStatusTitle`,
                  `olfps`.`name`                                                AS `paymentStatus`,
                  `ol`.`orderLineFriendPaymentDate`                             AS `paymentDate`,
                   ol.remoteShopSellerId                                        as remoteShopSellerId,   
                  `ol`.remoteOrderSellerId as remoteOrderSellerId,
                   s2.name as remoteShopName,  
                  ifnull((SELECT l.time
                   FROM Log AS l
                   WHERE concat(`ol`.`id`, '-', `ol`.`orderId`) = l.stringId and l.actionName = 'ShippedByFriend' 
                   LIMIT 1),'Non Spedito')                                                     AS 'friendShipmentTime'
                FROM
                  (((((((((`Order` AS `o`
                  
                    JOIN `OrderLine` AS `ol` ON `o`.`id` = `ol`.`orderId`)
                    JOIN `Shop` AS `s` ON `ol`.`shopId` = `s`.`id`)
                    JOIN `OrderStatus` AS `os` ON `o`.`status` = `os`.`code`)
                    JOIN `OrderLineStatus` AS `ols` ON `ol`.`status` = `ols`.`code`)
                    JOIN `User` AS `u` ON `u`.`id` = `o`.`userId`)
                    JOIN `Product` AS `p` ON `ol`.`productId` = `p`.`id` AND `ol`.`productVariantId` = `p`.`productVariantId`)
                     JOIN Shop AS s2 on ol.remoteShopSellerId = s2.id)
                    #JOIN `Log` as l ON l.stringId = concat(ol.id, '-', o.id)
                    JOIN `ProductVariant` AS `pv` ON `p`.`productVariantId` = `pv`.`id`
                    JOIN `ProductSize` AS `ps` ON `ol`.`productSizeId` = `ps`.`id`)
                    JOIN `ProductBrand` AS `pb` ON `p`.`productBrandId` = `pb`.`id`)
                  JOIN `ProductSeason` AS `pse` ON `p`.`productSeasonId` = `pse`.`id`
                  LEFT JOIN (`InvoiceLineHasOrderLine` AS `ilhol`
                      JOIN Document AS `in` ON `in`.`id` = `ilhol`.`invoiceLineInvoiceId`
                      JOIN InvoiceType as `it` on `in`.`invoiceTypeId` = `it`.`id`)
                          ON `ol`.`orderId` = `ilhol`.orderLineOrderId AND `ol`.`id` = `ilhol`.`orderLineId`
                  LEFT JOIN `OrderLineFriendPaymentStatus` AS `olfps` ON `ol`.`orderLineFriendPaymentStatusId` = `olfps`.`id`
                  WHERE `ols`.`code` NOT IN ('ORD_ARCH', 'CRT', 'CRT_MRG') and ol.status='ORD_PENDING' OR 
                        ol.status='ORD_WAIT' OR 
                        ol.status='ORD_PENDING' OR 
                        ol.status='ORD_FRND_SENT' OR
                        ol.status='ORD_FRND_SNDING' OR 
                        ol.status='ORD_CHK_IN' OR 
                        ol.status='ORD_PCK_CLI' OR
                         ol.status='ORD_PCK_CLI' OR
                        ol.status='ORD_LAB' OR
                        ol.status='ORD_MAIL_PREP_C'".
                        $filterSql ."  $DDThaving  ";


        $datatable = new CDataTables($query,['id', 'orderId'],$_GET, true);
        $datatable->addCondition('shopId',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        if (!$allShops) {
            $datatable->addCondition('orderLineStatusCode',
                [
                    'ORD_MISSING',
                    'ORD_CANCEL',
                    'ORD_ARCH',
                    'ORD_PENDING',
                    'ORD_WAIT',
                    'ORD_LAB',
                    'ORD_ERR_SEND'
                ],
                true
            );
        }

        $orderLines = \Monkey::app()->repoFactory->create('OrderLine')
            ->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('OrderLine')
            ->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = \Monkey::app()->repoFactory->create('OrderLine')
            ->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $orderStatuses = \Monkey::app()->repoFactory->create('OrderStatus')->findAll();
        $colorStatus = [];
        foreach($orderStatuses as $orderStatus){
            $colorStatus[$orderStatus->code] = $orderStatus->color;
        }

        $orderLineStatuses = \Monkey::app()->repoFactory->create('OrderLineStatus')->findAll();
	    $plainLineStatuses = [];
        $colorLineStatuses = [];
	    foreach($orderLineStatuses as $orderLineStatus){
			$plainLineStatuses[$orderLineStatus->code] = $orderLineStatus->title;
            $colorLineStatuses[$orderLineStatus->code] = $orderLineStatus->colore;
	    }

        $orderLineStatuses = \Monkey::app()->repoFactory->create('OrderLineStatus')->findAll();
        $plainLineStatuses = [];
        $colorLineStatuses = [];
        foreach($orderLineStatuses as $orderLineStatus){
            $plainLineStatuses[$orderLineStatus->code] = $orderLineStatus->title;
            $colorLineStatuses[$orderLineStatus->code] = $orderLineStatus->colore;
        }

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totlalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        $i = 0;

        $lR = \Monkey::app()->repoFactory->create('Log');
        $orderLineStatuses = \Monkey::app()->repoFactory->create('OrderLineStatus')->findAll()->toArray();
        foreach ($orderLineStatuses as $k => $v) {
            $orderLineStatuses[$k] = $v->toArray();
        }
        foreach ($orderLines as $v) {
            /** @var COrderLine $v */
	        /** ciclo le righe */
            $response['data'][$i]['id'] = $v->id;
            $findRemoteShopSeller=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$v->remoteShopSellerId]);
            $response['data'][$i]['remoteOrderSellerId']=$v->remoteOrderSellerId;
            $response['data'][$i]['remoteShopName']=$findRemoteShopSeller->name;
            $response['data'][$i]['orderCode'] = $v->printId();
            $response['data'][$i]['line_id'] = $v->printId();
            $response['data'][$i]['orderId'] = $v->orderId;
            $response['data'][$i]['code'] = $v->product->printId();
            $response['data'][$i]['size'] = $v->productSize->name;
            $response['data'][$i]['dummyPicture'] =
                '<a href="#1" class="enlarge-your-img"><img width="50" src="' .
                $v->product->getDummyPictureUrl() . '" /></a>';
            $statusCode = $v->orderLineStatus->code;

            if (!$allShops &&  9 < $v->orderLineStatus->id) {
                //$lastSuitable = $olsR->getLastStatusSuitableByFriend($v->printId(), $v->shopId);
                //if ($lastSuitable)
                $lineStatus = '<span style="color: #999">Chiuso</span>';
            } else {
                if (!$allShops && false !== strpos($plainLineStatuses[$statusCode], 'friend')) {
                    $editedStatus =
                        str_replace('al friend', '', str_replace('dal friend', '', $plainLineStatuses[$statusCode]));
                } else {
                    $editedStatus = $plainLineStatuses[$statusCode];
                }
                $lineStatus = '<span style="color:' . $colorLineStatuses[$statusCode] . '" ">' .
                    $editedStatus .
                    '</span>';
            }

            $response['data'][$i]['orderLineStatusTitle'] = $lineStatus;
            $time = strtotime($v->order->orderDate);
            $response['data'][$i]['orderDate'] = date("d/m/Y H:i:s", $time);
            $response['data'][$i]['brand'] = $v->product->productBrand->name;
            $response['data'][$i]['season'] = $v->product->productSeason->name;
            $response['data'][$i]['cpf'] = $v->product->printCpf();
            $response['data'][$i]['extId'] = $v->productSku->getExternalId();
            $response['data'][$i]['shopName'] = $v->shop->title;
            $response['data'][$i]['shopId'] = $v->shop->id;
            if ($v->orderLineFriendPaymentStatusId) {
                $fpsColor = $olfpsR->getColor($v->orderLineFriendPaymentStatusId);
                $fps = '<span style="color: ' . $fpsColor . ';">' . $v->orderLineFriendPaymentStatus->name . '</span>';
            } else {
                $fps = '-';
            }
            $response['data'][$i]['paymentStatus'] = $fps;
            $datePay = '-';
            if ($v->orderLineFriendPaymentDate) {
                $datePay = implode('/', array_reverse(explode('-',explode(' ', $v->orderLineFriendPaymentDate)[0])));
            }
            $response['data'][$i]['paymentDate'] = $datePay;
            $response['data'][$i]['fullPrice'] = number_format($v->fullPrice, 2, ',', '');
            $response['data'][$i]['activePrice'] = number_format($v->activePrice, 2, ',', '');
            $response['data'][$i]['friendRevenue'] = number_format($v->friendRevenue, 2, ',', '');
            $response['data'][$i]['friendRevVat'] = SPriceToolbox::grossPriceFromNet($v->friendRevenue, $vat, true);
            $response['data'][$i]['friendRevVat'] = SPriceToolbox::grossPriceFromNet($v->friendRevenue, $vat, true);
            $document = $olR->getFriendInvoice($v);
            $response['data'][$i]['invoiceAll'] = '<span class="small">';
            $response['data'][$i]['invoiceNumber'] = '-';
            $response['data'][$i]['creditNoteNumber'] = '-';
            $response['data'][$i]['transDocNumber'] = '-';
               if ($document) {
                   $response['data'][$i]['invoiceAll'] .= $document->number . ' (id:' . $document->id . ')<br />';
                   $response['data'][$i]['invoiceNumber'] = $document->number . ' (id:' . $document->id . ')';
               }
            $creditNote = $olR->getFriendCreditNote($v);
            if ($creditNote) {
                $response['data'][$i]['invoiceAll'] .= 'Reso: ' . $creditNote->number . ' (id:' . $creditNote->id . ')<br />';
                $response['data'][$i]['creditNoteNumber'] = $creditNote->number . ' (id:' . $creditNote->id . ')';
            }
            $transDoc = $olR->getFriendTransDoc($v);
            if ($transDoc) {
                $response['data'][$i]['invoiceAll'] .= 'DDT: ' . $transDoc->number . ' (id:' . $transDoc->id . ')';
                $response['data'][$i]['transDocNumber'] = $transDoc->number . ' (id:' . $transDoc->id . ')';
            }
            $response['data'][$i]['invoiceAll'].= '</span>';
            $lOC = $lR->findBy(
                ['stringId' => $v->printId(), 'entityName' => 'OrderLine', 'actionName' => 'OrderStatusLog']
            );
            $printActs = '';
            foreach($lOC as $l) {
                $key = array_search($l->eventValue, array_column($orderLineStatuses, 'code'));
                if (
                    (4 < $orderLineStatuses[$key]['id']
                        && 11 > $orderLineStatuses[$key]['id']
                        && 9 != $orderLineStatuses[$key]['id']
                    )
                    || 19 == $orderLineStatuses[$key]['id']) {
                    $printActs .= $orderLineStatuses[$key]['title']
                        . ': ' . STimeToolbox::EurFormattedDateTime($l->time) . '<br />';
                }
            }
            $response['data'][$i]['friendTimes'] =
                ($printActs) ? '<span class="small">' . $printActs . '</span>' : 'Nessun record';
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
            if (
                isset($dbOrderingColumns[$column['column']])
                && $dbOrderingColumns[$column['column']]['column'] !== null
            ) {
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