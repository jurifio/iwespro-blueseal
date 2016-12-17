<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\domain\entities\CProductSku;

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
                  `o`.`id`                                               AS `id`,
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
                  o.note as notes
                FROM ((((((((((`Order` `o`
                  JOIN `User` `u`) 
                  JOIN `UserDetails` `ud`) 
                  JOIN `OrderPaymentMethod` `opm`) 
                  JOIN `OrderStatus` `os`) 
                  JOIN `OrderStatusTranslation` `oshl`) 
                  JOIN `OrderLine` `ol`) 
                  JOIN `Shop` `s`) 
                  JOIN `OrderLineStatus` `ols`) 
                  JOIN `Product` `p`) 
                  JOIN `ProductBrand` `pb`)
                WHERE ((`o`.`userId` = `u`.`id`) AND (`ud`.`userId` = `u`.`id`) AND (`o`.`orderPaymentMethodId` = `opm`.`id`) AND
                       (`o`.`status` = `os`.`code`) AND (`o`.`status` LIKE 'ORD%') AND (`oshl`.`orderStatusId` = `os`.`id`) AND
                       (`ol`.`orderId` = `o`.`id`) AND (`s`.`id` = `ol`.`shopId`) AND (`ol`.`productId` = `p`.`id`) AND
                       (`ol`.`productVariantId` = `p`.`productVariantId`) AND (`p`.`productBrandId` = `pb`.`id`) AND
                       (`ol`.`status` = `ols`.`code` ))";
        $datatable = new CDataTables($sql, ['id'], $_GET,true);
        $datatable->addCondition('statusCode', ['ORD_CANCEL'], true);
        $datatable->addSearchColumn('orderLineStatus');
        $datatable->addSearchColumn('shop');
        $datatable->addSearchColumn('productBrand');
        $datatable->addSearchColumn('email');

        $q = $datatable->getQuery();
        $orders = $this->app->repoFactory->create('Order')->em()->findBySql($q, $datatable->getParams());
        $count = $this->app->repoFactory->create('Order')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = $this->app->repoFactory->create('Order')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $orderStatuses = $this->app->repoFactory->create('OrderStatus')->findAll();
        $colorStatus = [];
        foreach ($orderStatuses as $orderStatus) {
            $colorStatus[$orderStatus->code] = $orderStatus->color;
        }

        $orderLineStatuses = $this->app->repoFactory->create('OrderLineStatus')->findAll();
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
            $paidAmount = isset($val->paidAmount) ? $val->paidAmount : 0;
            if ($val->lastUpdate != null) {
                $timestamp = time() - strtotime($val->lastUpdate);
                $day = date("z", $timestamp);
                $h = date("H", $timestamp);
                $m = date("i", $timestamp);
                $since = $day . ' giorni ' . $h . ":" . $m . " fa";
            }
            $row["id"] = '<a href="' . $opera . $val->id . '" >' . $val->id . '</a>';
            if ($alert) $row["id"] .= " <i style=\"color:red\"class=\"fa fa-exclamation-triangle\"></i>";

            $row["orderDate"] = $orderDate;
            $row["lastUpdate"] = isset($since) ? $since : "Mai";
            $row["user"] = '<span>' . $val->userDetails->name . " " . $val->userDetails->surname . '</span><br /><span>' . $val->user->email . '</span>';
            if (isset($val->rbacRole) && count($val->rbacRole) > 0) {
                $row["user"] .= ' <i class="fa fa-diamond"></i>';
            }

            $row["status"] = "<span style='color:" . $colorStatus[$val->status] . "'>" . $val->orderStatus->orderStatusTranslation->getFirst()->title . "</span>";
            $row["dareavere"] = (($val->netTotal !== $paidAmount) && ($val->orderPaymentMethodId !== 5)) ? "<span style='color:#FF0000'>" . number_format($val->netTotal, 2) . ' / ' . number_format($paidAmount, 2) . "</span>" : number_format($val->netTotal, 2) . ' / ' . number_format($paidAmount, 2);
            $row["payment"] = $val->orderPaymentMethod->name;
            $row["notes"] = wordwrap($val->note,50,'</br>');

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
        throw new \Exception();
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