<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\domain\entities\CProductSku;
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
class COrderListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                  `o`.`id` as `id`,
                  `o`.remoteOrderId                                           as remoteOrderId,
                  concat(`ud`.`name`, ' ', `ud`.`surname`)               AS `user`,
                  `ud`.`name`                                            AS `name`,
                  `ud`.`surname`                                         AS `surname`,
                  `u`.`email`                                            AS `email`,
                  `o`.`orderDate`                                        AS `orderDate`,
                  `o`.`lastUpdate`                                       AS `lastUpdate`,
                  concat(`ol`.`productId`, '-', `ol`.`productVariantId`,' ', s.title, ' ', p.itemno, ' ', `pb`.`name`, ' ', `ols`.`title` ) AS `product`,
                  `s`.`title`                                            AS `shop`,
                  `os`.`title`                                           AS `status`,
                  `o`.`status`                                           AS `statusCode`,
                  `opm`.`name`                                           AS `payment`,
                  `ols`.`title`                                          AS `orderLineStatus`,
                  `pb`.`name`                                            AS `productBrand`,
                  concat(`o`.`netTotal`, '/' , `o`.`paidAmount`)         AS `dareavere`,
                  if(`o`.`paidAmount` > 0, 'sìsi', 'no')                 AS `paid`,
                  o.paymentDate AS paymentDate,
                  o.note AS notes,
                  group_concat(c.name) as orderSources
                FROM `Order` `o`
                  JOIN `User` `u` ON `o`.`userId` = `u`.`id`
                  JOIN `UserDetails` `ud` ON `ud`.`userId` = `u`.`id`
                  JOIN `OrderPaymentMethod` `opm` ON `o`.`orderPaymentMethodId` = `opm`.`id`
                  JOIN `OrderStatus` `os` ON `o`.`status` = `os`.`code`
                  JOIN `OrderStatusTranslation` `oshl` ON `oshl`.`orderStatusId` = `os`.`id`
                  JOIN `OrderLine` `ol` ON `ol`.`orderId` = `o`.`id`
                  JOIN `Shop` `s` ON `s`.`id` = `ol`.`shopId`
                  JOIN `OrderLineStatus` `ols` ON `ol`.`status` = `ols`.`code`
                  JOIN `Product` `p` ON `ol`.`productId` = `p`.`id` AND `ol`.`productVariantId` = `p`.`productVariantId`
                  JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
                  LEFT JOIN ( 
                    CampaignVisitHasOrder cvho JOIN 
                    Campaign c ON cvho.campaignId = c.id) ON o.id = cvho.orderId
                WHERE `o`.`status` LIKE 'ORD%' GROUP BY ol.id, ol.orderId";

        $critical = \Monkey::app()->router->request()->getRequestData('critical');
        $countersign = \Monkey::app()->router->request()->getRequestData('countersign');
        if ($critical) {
            $sql .= " AND ((`o`.`status` LIKE 'ORD_PENDING' AND `ol`.`status` NOT LIKE 'ORD_FRND_CANC' ) " .
                " OR (`ol`.`status` LIKE 'ORD_FRND_OK' AND `os`.`id` NOT IN (8,9,10,13,18) AND `ols`.`id` < 8) AND (`ol`.`status` NOT LIKE 'ORD_CANCEL' OR `ol`.`status` NOT LIKE 'ORD_RETURNED') and (`o`.`orderPaymentMethodId` <> 5 AND `o`.`paymentDate` is NULL))";
        } elseif ($countersign) {
            $sql .= " AND `o`.`orderPaymentMethodId` = 5 AND `o`.`paymentDate` is null AND `os`.`code` LIKE 'ORD_SHIPPED'";
        }
        $toSend = \Monkey::app()->router->request()->getRequestData('toSend');
        if ($toSend)
            $sql .= "AND (
                `ols`.`id` > 4 AND `os`.`id` NOT IN (8,13,18) 
                AND (
                        (`o`.`paymentDate` IS NOT NULL AND `o`.`orderPaymentMethodId` <> 5)
                         OR 
                        (`o`.`paymentDate` IS NULL AND `o`.`orderPaymentMethodId` = 5)
                    ))";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);
        $datatable->addSearchColumn('orderLineStatus');
        $datatable->addSearchColumn('shop');
        $datatable->addSearchColumn('productBrand');
        $datatable->addSearchColumn('email');

        $q = $datatable->getQuery();
        $p = $datatable->getParams();
        $orders = \Monkey::app()->repoFactory->create('Order')->em()->findBySql($q, $p);
        $count = \Monkey::app()->repoFactory->create('Order')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = \Monkey::app()->repoFactory->create('Order')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $orderStatuses = \Monkey::app()->repoFactory->create('OrderStatus')->findAll();
        $colorStatus = [];
        foreach ($orderStatuses as $orderStatus) {
            $colorStatus[$orderStatus->code] = $orderStatus->color;
        }

        $orderLineStatuses = \Monkey::app()->repoFactory->create('OrderLineStatus')->findAll();
        $plainLineStatuses = [];
        $colorLineStatus = [];
        foreach ($orderLineStatuses as $orderLineStatus) {
            $plainLineStatuses[$orderLineStatus->code] = $orderLineStatus->title;
            $colorLineStatus[$orderLineStatus->code] = $orderLineStatus->colore;
        }

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totlalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "ordini/aggiungi?order=";

        foreach ($orders as $val) {
            $row = [];
            /** ciclo le righe */
            $row["product"] = "";
            $alert = false;
            foreach ($val->orderLine as $line) {
                try {
                    /** @var CProductSku $sku */
                    $sku = \bamboo\domain\entities\CProductSku::defrost($line->frozenProduct);
                    $sku->setEntityManager($this->app->entityManagerFactory->create('ProductSku'));

                    $code = $sku->shop->name . ' ' . $sku->printPublicSku() . " (" . $sku->product->productBrand->name . ")";
                    if ($line->orderLineStatus->notify === 1) $alert = true;
                } catch (\Throwable $e) {
                    $code = 'non trovato';
                }

                $row["product"] .= "<span style='color:" . $colorLineStatus[$line->status] . "'>" . $code . " - " . $plainLineStatuses[$line->status] . "</span>";
                $row["product"] .= "<br/>";
            }


            $orderDate = date("D d-m-y H:i", strtotime($val->orderDate));
            $paidAmount = ($val->paidAmount) ? $val->paidAmount : 0;
            if ($val->lastUpdate != null) {
                $timestamp = time() - strtotime($val->lastUpdate);
                $day = date("z", $timestamp);
                $h = date("H", $timestamp);
                $m = date("i", $timestamp);
                $since = $day . ' giorni ' . $h . ":" . $m . " fa";
            }
            $row["DT_RowId"] = $val->id;
            $row['remoteOrderId']=$val->remoteOrderId;
            $row["id"] = '<a href="' . $opera . $val->id . '" >' . $val->id . '</a>';
            if ($alert) $row["id"] .= " <i style=\"color:red\"class=\"fa fa-exclamation-triangle\"></i>";

            $row["orderDate"] = $orderDate;
            $row["lastUpdate"] = isset($since) ? $since : "Mai";
            $row["user"] =
                '<a href="/blueseal/utente?userId=' . $val->user->printId() . '"><span>' . $val->user->getFullName()
                . '</span><br /><span>' . $val->user->email . '</span></a>';
            if (isset($val->user->rbacRole) && count($val->user->rbacRole) > 0) {
                $row["user"] .= '<i class="fa fa-diamond"></i>';
            } elseif (!empty($val->user->userDetails->note)) {
                $row["user"] .= '<i class="fa fa-sticky-note-o" aria-hidden="true"></i>';
            }
            try {
                //TODO CHECK THIS WROOOONG
                $row['user'] .= '<br />' . $val->billingAddress->country->name;
            } catch (\Throwable $e) {
            }


            $row["status"] = "<span style='color:" . $colorStatus[$val->status] . "'>" . $val->orderStatus->orderStatusTranslation->getFirst()->title . "</span>";
            $paid = ($paidAmount) ? 'Sì' : 'No';
            $netTotal = SPriceToolbox::formatToEur($val->netTotal);
            $row["dareavere"] = (($val->netTotal !== $paidAmount) && ($val->orderPaymentMethodId !== 5)) ? "<span style='color:#FF0000'>" . $netTotal . "</span>" : $netTotal;
            $row['paid'] = $paid;
            $row["paymentDate"] = $val->paymentDate;
            $row["payment"] = $val->orderPaymentMethod->name;
            $row["notes"] = wordwrap($val->note, 50, '</br>');
            $userDetails = $val->user->userDetails;
            $note = ($userDetails) ? wordwrap($val->user->userDetails->note, 50, '</br>') : '-';
            $row["userNote"] = $note;
            $row["orderSources"] = [];
            foreach ($val->campaignVisitHasOrder as $campaignVisitHasOrder)
            {
                $row["orderSources"][] = $campaignVisitHasOrder->campaignVisit->campaign->name.' - '.$campaignVisitHasOrder->campaignVisit->timestamp.' - '.$campaignVisitHasOrder->campaignVisit->cost.'€';
            }
            $row["orderSources"] = implode(',<br>',$row["orderSources"]);
            $response['data'][] = $row;
        }
        return json_encode($response);
    }

    public function post()
    {
        throw new \Exception();
    }

    public function delete()
    {
        $orderId = \Monkey::app()->router->request()->getRequestData('orderId');
        if (!$orderId) throw new \Exception('Id ordine non pervenuto. Non posso cancellarlo');

        $oR = \Monkey::app()->repoFactory->create('Order');
        $soR = \Monkey::app()->repoFactory->create('StorehouseOperation');
        $logR = \Monkey::app()->repoFactory->create('Log');
        $solR = \Monkey::app()->repoFactory->create('StorehouseOperationLine');
        $ushoR = \Monkey::app()->repoFactory->create('UserSessionHasOrder');
        $cvhoR = \Monkey::app()->repoFactory->create('CampaignVisitHasOrder');

        $dba = \Monkey::app()->dbAdapter;

        $order = $oR->findOne([$orderId]);
        if (!$order) throw new BambooException('L\'id ordine fornito non corrisponde a nessun ordine');

        if ('ORD_CANCEL' === $order->status || 'ORD_PENDING' === $order->status) {
            \Monkey::app()->repoFactory->beginTransaction();
            try {
                $usoC = $ushoR->findBy(['orderId' => $orderId]);
                foreach ($usoC as $uso) {
                    $uso->delete();
                }

                $iR = \Monkey::app()->repoFactory->create('Invoice');
                $iC = $iR->findBy(['orderId' => $orderId]);
                if ($iC->count()) throw new BambooException('Non possono essere cancellati ordini contenenti fatture');

                $qtyToRestore = [];
                foreach ($order->orderLine as $ol) {

                    if (!array_key_exists($ol->productId . '-' . $ol->productVariantId . '-' . $ol->productSizeId . '-' . $ol->shopId, $qtyToRestore)) {
                        $qtyToRestore[$ol->productId . '-' . $ol->productVariantId . '-' . $ol->productSizeId . '-' . $ol->shopId] = 0;
                    }
                    $qtyToRestore[$ol->productId . '-' . $ol->productVariantId . '-' . $ol->productSizeId . '-' . $ol->shopId] += 1;

                    $log = $logR->findOneBy([
                        'entityName' => 'OrderLine',
                        'stringId' => $ol->id . '-' . $ol->orderId,
                        'eventValue' => 'ORD_FRND_Ok'
                    ]);

                    if ($log) {
                        $utime = strtotime($log->time);
                        $endTime = $utime + 3;

                        $query = "SELECT 
                              s.id AS storehouseOperationId,
                              sl.shopId AS shopId,
                              sl.storehouseId AS storehouseId,
                              sl.productId AS productId,
                              sl.productVariantId AS productVariantId,
                              sl.productSizeId AS productSizeId
                              FROM `StorehouseOperation` AS `s` JOIN `StorehouseOperationLine` AS `sl` 
                              ON `s`.`id` = `sl`.`storehouseOperationId` 
                              WHERE `s`.`shopId` = ?  AND `sl`.`productId` = ? AND `sl`.`productVariantId` = ? 
                              AND `sl`.`productSizeId` = ? AND `s`.`creationDate` >= ? AND `s`.`creationDate` < ?";
                        $res = $dba->query($query, [
                            $ol->shopId,
                            $ol->productId,
                            $ol->productVariantId,
                            $ol->productSizeId,
                            $utime,
                            $endTime
                        ])->fetch();

                        if ($res) {
                            $solC = $solR->findBy(
                                [
                                    'storehouseOperationId' => $res['storehouseOperationId'],
                                    'shopId' => $res['shopId'],
                                    'storehouseId' => $res['storehouseId'],
                                    'productId' => $res['productId'],
                                    'productVariantId' => $res['productVariantId'],
                                    'productSizeId' => $res['productSizeId']
                                ]);
                            foreach ($solC as $sol) {
                                $sol->delete();
                            }
                        }

                        $solC = $solR->findBy([
                            'storehouseOperationId' => $res['storehouseOperationId'],
                            'shopId' => $res['shopId'],
                            'storehouseId' => $res['storehouseId']
                        ]);
                        if (!$solC->count()) {
                            $soDel = $soR->findBy([
                                'storehouseOperationId' => $res['storehouseOperationId'],
                                'shopId' => $res['shopId'],
                                'storehouseId' => $res['storehouseId']
                            ]);
                            $soDel->delete();
                        }

                    }
                }

                foreach ($order->orderHistory as $oh) {
                    $oh->delete();
                }


                foreach ($order->orderLine as $ol) {

                    $logolz = $logR->findBy(['stringId' => $ol->printId(), 'entityName' => 'OrderLine']);
                    foreach ($logolz as $logol) {
                        $logol->delete();
                    }
                    $ol->delete();
                }

                $cvho = $cvhoR->findBy(['orderId' => $order->id]);

                foreach ($cvho as $cvhoSingle) {
                    $cvhoSingle->delete();
                }


                $logOrderz = $logR->findBy(['stringId' => $orderId, 'entityName' => 'Order']);
                foreach ($logOrderz as $logOrd) {
                    $logOrd->delete();
                }
                $order->delete();

                \Monkey::app()->repoFactory->commit();
                return "Ordine eliminato!";
            } catch (BambooException $e) {
                \Monkey::app()->repoFactory->rollback();
                \Monkey::app()->router->response()->raiseProcessingError();
                return $e->getMessage();
            }
        }
        return "L'ordine deve essere nello stato \"Cancellato\" o \"In attesa di pagamento\" per poter procedere!";
    }

    public function orderBy()
    {
        $dtOrderingColumns = $_GET['order'];
        $dbOrderingColumns = [
            ['column' => 'o.id'],
            ['column' => 'o.creationDate'],
            ['column' => 'o.lastUpdate']
        ];
        $dbOrderingDefault = [
            ['column' => 'o.creationDate', 'dir' => 'desc']
        ];

        $sqlOrder = " ORDER BY ";
        foreach ($dtOrderingColumns as $column) {
            if (isset($dbOrderingColumns[$column['column']]) && $dbOrderingColumns[$column['column']]['column'] !== null) {
                $sqlOrder .= $dbOrderingColumns[$column['column']]['column'] . " " . $column['dir'] . ", ";
            }
        }
        if (substr($sqlOrder, -1, 2) != ', ') {
            foreach ($dbOrderingDefault as $column) {
                $sqlOrder .= $column['column'] . ' ' . $column['dir'] . ', ';
            }
        }
        return rtrim($sqlOrder, ', ');
    }
}