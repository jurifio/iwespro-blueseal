<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\utils\price\SPriceToolbox;
use bamboo\utils\time\STimeToolbox;

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
class CFriendOrderListAjaxController extends AAjaxController
{

    public function get()
    {
        $olfpsR = \Monkey::app()->repoFactory->create('OrderLineFriendPaymentStatus');
        $cR = \Monkey::app()->repoFactory->create('Configuration');
        $vat = $cR->findOneBy(['name' => 'main vat'])->value;
        $user = $this->app->getUser();
        $allShops = $user->hasPermission('allShops');
        // Se non è allshop devono essere visualizzate solo le linee relative allo shop e solo a un certo punto di avanzamento

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
                  ifnull(`in`.`number`, 'non assegnata')                        AS `invoiceNumber`,
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
                  ifnull((SELECT l.time
                   FROM Log AS l
                   WHERE concat(`ol`.`id`, '-', `ol`.`orderId`) = l.stringId and l.actionName = 'ShippedByFriend' 
                   LIMIT 1),'Non Spedito')                                                     AS 'friendShipmentTime'
                FROM
                  ((((((((`Order` AS `o`
                    JOIN `OrderLine` AS `ol` ON `o`.`id` = `ol`.`orderId`)
                    JOIN `Shop` AS `s` ON `ol`.`shopId` = `s`.`id`)
                    JOIN `OrderStatus` AS `os` ON `o`.`status` = `os`.`code`)
                    JOIN `OrderLineStatus` AS `ols` ON `ol`.`status` = `ols`.`code`)
                    JOIN `User` AS `u` ON `u`.`id` = `o`.`userId`)
                    JOIN `Product` AS `p` ON `ol`.`productId` = `p`.`id` AND `ol`.`productVariantId` = `p`.`productVariantId`)
                    #JOIN `Log` as l ON l.stringId = concat(ol.id, '-', o.id)
                    JOIN `ProductVariant` AS `pv` ON `p`.`productVariantId` = `pv`.`id`
                    JOIN `ProductSize` AS `ps` ON `ol`.`productSizeId` = `ps`.`id`)
                    JOIN `ProductBrand` AS `pb` ON `p`.`productBrandId` = `pb`.`id`)
                  JOIN `ProductSeason` AS `pse` ON `p`.`productSeasonId` = `pse`.`id`
                  LEFT JOIN (`InvoiceLineHasOrderLine` AS `ilhol`
                  JOIN InvoiceNew AS `in` ON `in`.`id` = `ilhol`.`invoiceLineInvoiceId`)
                    ON `ol`.`orderId` = `ilhol`.orderLineOrderId AND `ol`.`id` = `ilhol`.`orderLineId`
                  LEFT JOIN `OrderLineFriendPaymentStatus` AS `olfps` ON `ol`.`orderLineFriendPaymentStatusId` = `olfps`.`id`";

        $datatable = new CDataTables($query,['id', 'orderId'],$_GET, true);
        $datatable->addCondition(
            'orderLineStatusCode',
            ['ORD_CANCEL', 'ORD_ARCH', 'CRT', 'CRT_MRG'],
            true);
        $datatable->addCondition('shopId',$this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());
        if (!$allShops) {
            $datatable->addCondition('orderLineStatusCode',
                [
                    'ORD_MISSING',
                    'ORD_CANCEL',
                    'ORD_ARCH',
                    'ORD_PENDING',
                    'ORD_WAIT',
                    'ORD_LAB',
                    'ORD_FRND_SNDING',
                    'ORD_ERR_SEND'
                ],
                true
            );
        }

        $orderLines = $this->app->repoFactory->create('OrderLine')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('OrderLine')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = $this->app->repoFactory->create('OrderLine')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $orderStatuses = $this->app->repoFactory->create('OrderStatus')->findAll();
        $colorStatus = [];
        foreach($orderStatuses as $orderStatus){
            $colorStatus[$orderStatus->code] = $orderStatus->color;
        }

        $orderLineStatuses = $this->app->repoFactory->create('OrderLineStatus')->findAll();
	    $plainLineStatuses = [];
        $colorLineStatuses = [];
	    foreach($orderLineStatuses as $orderLineStatus){
			$plainLineStatuses[$orderLineStatus->code] = $orderLineStatus->title;
            $colorLineStatuses[$orderLineStatus->code] = $orderLineStatus->colore;
	    }

        $orderLineStatuses = $this->app->repoFactory->create('OrderLineStatus')->findAll();
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

        foreach ($orderLines as $v) {
	        /** ciclo le righe */
            $response['data'][$i]['id'] = $v->id;
            $response['data'][$i]['orderCode'] = $v->printId();
            $response['data'][$i]['line_id'] = $v->printId();
            $response['data'][$i]['orderId'] = $v->orderId;
            $response['data'][$i]['code'] = $v->product->id . "-" . $v->product->productVariantId;
            $response['data'][$i]['size'] = $v->productSize->name;
            $response['data'][$i]['dummyPicture'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $v->product->getDummyPictureUrl() . '" /></a>';
            $statusCode = $v->orderLineStatus->code;

            if (!$allShops &&  9 < $v->orderLineStatus->id) {
                //$lastSuitable = $olsR->getLastStatusSuitableByFriend($v->printId(), $v->shopId);
                //if ($lastSuitable)
                $lineStatus = '<span style="color: #999">Chiuso</span>';
            } else {
                if (!$allShops && false !== strpos($plainLineStatuses[$statusCode], 'friend')) {
                    $editedStatus = str_replace('al friend', '', str_replace('dal friend', '', $plainLineStatuses[$statusCode]));
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
            $response['data'][$i]['cpf'] = $v->product->itemno . ' # ' . $v->product->productVariant->name;
            $response['data'][$i]['shopName'] = $v->shop->title;
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
            $invoiceLine = $v->invoiceLine->getFirst();
            $response['data'][$i]['invoiceNumber'] = ($invoiceLine) ? $invoiceLine->invoiceNew->number . ' (id:' . $invoiceLine->invoiceId . ')' : 'non assegnata' ;
            $l = $lR->findOneBy(['stringId' => $v->printId(), 'ActionName' => 'ShippedByFriend']);
            $response['data'][$i]['friendShipmentTime'] = ($l) ? STimeToolbox::EurFormattedDateTime($l->time) : 'Non spedito';
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