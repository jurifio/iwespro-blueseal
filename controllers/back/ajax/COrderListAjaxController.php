<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\blueseal\business\CDownloadFileFromDb;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\domain\entities\CInvoiceDocument;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;
use bamboo\utils\price\SPriceToolbox;
use bamboo\domain\entities\CUserAddress;
use PDO;
use PDOException;

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
        $perm = \Monkey::app()->getUser()->hasPermission('allShops');

        $productHasShopDestinationRepo = \Monkey::app()->repoFactory->create('ProductHasShopDestination');
        $sql = "SELECT
                  concat(`o`.`id`,'', `oshl`.`title` , ' ', if(`o`.`paidAmount` > 0, 'Pagato', 'Non Pagato') )                                              AS `id`,
                  concat(`ud`.`name`, ' ', `ud`.`surname`,' ',s2.title, ' ', u.email, ' ',o.remoteOrderSellerId)               AS `user`,
                  `ud`.`name`                                            AS `name`,
                  `ud`.`surname`                                         AS `surname`,
                  `u`.`email`                                            AS `email`,
                  `o`.`orderDate`                                        AS `orderDate`,
                  `o`.`lastUpdate`                                       AS `lastUpdate`,
                  concat(`ol`.`productId`, '-', `ol`.`productVariantId`, ' ', s.title, ' ', p.itemno, ' ', `pb`.`name`, ' ', `ols`.`title`, ' ',`ol`.`remoteOrderSupplierId`) AS `product`,
                  `s`.`title`                                            AS `shop`,
                  `os`.`title`                                           AS `status`,
                  `o`.`status`                                           AS `statusCode`,
                  `opm`.`name`                                           AS `payment`,
                  `ols`.`title`                                          AS `orderLineStatus`,
                  `pb`.`name`                                            AS `productBrand`,
                  if(`o`.`paidAmount` > 0, 'sìsi', 'no')                 AS `paid`,
                  o.paymentDate AS paymentDate,
                  o.note AS notes,
                  ol.remoteOrderSupplierId as remoteOrderSuppllierId,
                  o.remoteOrderSellerId as remoteOrderSellerId,
                  o.remoteShopSellerId as remoteShopSellerId,  
                  `s2`.`title` as remoteShopSellerName,
                  o.marketplaceId as marketplaceId,
                  o.marketplaceOrderId as marketplaceOrderId,
                  group_concat(c.name) as orderSources
                FROM `Order` `o`
                  JOIN `User` `u` ON `o`.`userId` = `u`.`id`
                  JOIN `UserDetails` `ud` ON `ud`.`userId` = `u`.`id`
                  JOIN `OrderPaymentMethod` `opm` ON `o`.`orderPaymentMethodId` = `opm`.`id`
                  JOIN `OrderStatus` `os` ON `o`.`status` = `os`.`code`
                  JOIN `OrderStatusTranslation` `oshl` ON `oshl`.`orderStatusId` = `os`.`id`
                  LEFT JOIN `OrderLine` `ol` ON `ol`.`orderId` = `o`.`id`
                  JOIN `Shop` `s` ON `s`.`id` = `ol`.`shopId`
                  JOIN `Shop` `s2` ON `s2`.id = `ol`.`remoteShopSellerId`  
                  JOIN `OrderLineStatus` `ols` ON `ol`.`status` = `ols`.`code`
                  JOIN `Product` `p` ON `ol`.`productId` = `p`.`id` AND `ol`.`productVariantId` = `p`.`productVariantId`
                  JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
                  LEFT JOIN  `MarketplaceHasShop` mhsp ON  o.marketplaceId=mhsp.id
                  LEFT JOIN ( 
                    CampaignVisitHasOrder cvho JOIN 
                    Campaign c ON cvho.campaignId = c.id) ON o.id = cvho.orderId
                WHERE `o`.`status` LIKE 'ORD%'   GROUP BY ol.id, ol.orderId";

        //      WHERE `o`.`status` LIKE 'ORD%' AND `o`.`creationDate` > '2018-06-09 00:00:00' GROUP BY ol.id, ol.orderId";

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
        $datatable = new CDataTables($sql,['id'],$_GET,true);
        $datatable->addSearchColumn('orderLineStatus');
        $datatable->addSearchColumn('shop');
        $datatable->addSearchColumn('productBrand');
        $datatable->addSearchColumn('email');

        $q = $datatable->getQuery();
        $p = $datatable->getParams();
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $markeplaceRepo = \Monkey::app()->repoFactory->create('MarketplaceHasShop');
        $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
        $countryR = \Monkey::app()->repoFactory->create('Country');
        $orders = \Monkey::app()->repoFactory->create('Order')->em()->findBySql($q,$p);
        $count = \Monkey::app()->repoFactory->create('Order')->em()->findCountBySql($datatable->getQuery(true),$datatable->getParams());
        $totlalCount = \Monkey::app()->repoFactory->create('Order')->em()->findCountBySql($datatable->getQuery('full'),$datatable->getParams());

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

        /** @var COrder $val */
        foreach ($orders as $val) {
            $row = [];
            if (!$perm) {
                if ($val->status == 'ORD_PENDING' || $val->status == 'ORD_WAIT') {
                    continue;
                }
            }


            $row['marketplaceOrderId'] = $val->marketplaceOrderId;
            if ($val->marketplaceId != null && $val->marketplaceId != 0) {
                $findMarketplace = $markeplaceRepo->findOneBy(['prestashopId' => $val->marketplaceId]);
                $row['marketplaceName'] = $findMarketplace->name;
            } else {
                $row['marketplaceName'] = " ";
            }
            /*  if( $val->markeplaceOrderId =='') {
                  $row['marketplaceOrderId'] = 'No';
              }*/
            /** ciclo le righe */
            $row['supplier'] = "";
            $row["product"] = "";
            $alert = false;
            $orderParal = '';
            $rowOrderParal='';
            // $remoteOrderSupplierId = '';
            // $remoteShopSupplier = '';
            $rowOrderSupplier = "";
            foreach ($val->orderLine as $line) {
                if ($line->remoteShopSellerId != 44 && $line->remoteShopSellerId != '') {
                    if ($line->remoteShopSellerId != $line->shopId) {
                        $orderParal = '<i style="color:green"><b>PARALLELO</b><i>';
                        //    $remoteOrderSupplierId = $line->remoteOrderSupplierId;
                    }else{
                        $orderParal='No';
                    }
                } else {
                    $orderParal = 'No';
                    //    $remoteOrderSupplierId = '';
                }

                try {

                    /** @var CProductSku $sku */
                    //        $sku = \bamboo\domain\entities\CProductSku::defrost($line->frozenProduct);

                    $sku = \Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId' => $line->productId,'productVariantId' => $line->productVariantId,'productSizeId' => $line->productSizeId]);
                    //$sku->setEntityManager($this->app->entityManagerFactory->create('ProductSku'));
                    if (!is_null($line->remoteOrderSupplierId)) {
                        $supplier = $sku->shop->name;
                    } else {
                        $supplier = '';
                    }

                    $code = "spedisce " . $sku->shop->name . ' ' . $sku->printPublicSku() . " (" . $sku->product->productBrand->name . ")";
                    if ($line->orderLineStatus->notify === 1) $alert = true;
                    $skuParalId = $line->productId;


                    $skupParalVariantId = $line->productVariantId;
                    $skuParalSizeId = $line->productSizeId;
                    $skuParalShopId = $line->shopId;
                    $skuParal = $val->remoteShopSellerId;


                } catch (\Throwable $e) {
                    $code = 'non trovato';
                }
                $rowOrderSupplier .= $line->remoteOrderSupplierId;

                $row["product"] .= "<span style='color:" . $colorLineStatus[$line->status] . "'>" . $code . " - " . $plainLineStatuses[$line->status] . "</br>Taglia: " . $sku->productSize->name . "</span>";
                $row["product"] .= "<br/>";
                $row["product"] .= "<b>" . $supplier . " - " . $line->remoteOrderSupplierId . "<b><br />";
                $shipmentCollect = "";
                $findOrderLineHasShipment = \Monkey::app()->repoFactory->create('OrderLineHasShipment')->findBy(['orderLineId' => $line->id,'orderId' => $line->orderId]);
                foreach ($findOrderLineHasShipment as $shipment) {
                    $findShipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id' => $shipment->shipmentId]);
                    $findCarrier = \Monkey::app()->repoFactory->create('Carrier')->findOneBy(['id' => $findShipment->carrierId]);
                    if ($findShipment->deliveryDate != null && $findShipment->shipmentDate != null) {
                        $btnclass = 'btn btn-success';
                    } else if ($findShipment->deliveryDate == null && $findShipment->shipmentDate != null) {
                        $btnclass = 'btn btn-warning';
                    } else {
                        $btnclass = 'btn btn-light';
                    }
                    //https://www.gls-italy.com/index.php?option=com_gls&task=track_e_trace.getSpedizioneWeblabeling&format=raw&cn=MC1108&rf=MC590355157&lc=ita
                    if ($findShipment->carrierId == 2) {
                        $shipmentCollect .= '<button style="width: 200px ; height:32px;"  onclick="openTrackGlsDelivery(\'' . $findShipment->trackingNumber . '\');" class=' . $btnclass . '> <i class="fa fa-truck" aria-hidden="true"></i>->' . $findCarrier->name . '</button><br>' . $findShipment->trackingNumber . '<br><b>Id Spedizione: </b>' . $findShipment->id . '<br>';
                    } else {
                        $shipmentCollect .= '<button style="width: 200px ; height:32px;"  onclick="openTrackDelivery(\'' . $findShipment->trackingNumber . '\');" class=' . $btnclass . '> <i class="fa fa-truck" aria-hidden="true"></i>->' . $findCarrier->name . '</button><br>' . $findShipment->trackingNumber . '<br><b>Id Spedizione: </b>' . $findShipment->id . '<br>';
                        //  $shipmentCollect.= '<button onclick="openTrackDelivery(\'1Z463V1V6897807419\');" class="btn btn-light" role="button"><i class="fa fa-truck" aria-hidden="true"></i>1Z463V1V6897807419</button>';
                    }
                }
                $row['shipmentId'] = $shipmentCollect;

            }


            $orderDate = date("d-m-y H:i",strtotime($val->orderDate));
            $paidAmount = ($val->paidAmount) ? $val->paidAmount : 0;
            if ($val->lastUpdate != null) {
                $timestamp = time() - strtotime($val->lastUpdate);
                $day = date("z",$timestamp);
                $h = date("H",$timestamp);
                $m = date("i",$timestamp);
                $since = $day . ' giorni ' . $h . ":" . $m . " fa";
            }
            $row["DT_RowId"] = $val->id;

            $shopFind = $shopRepo->findOneBy(['id' => $val->remoteShopSellerId]);

            if ($shopFind == null) {

                $sellerShopName = 'Pickyshop';
            } else {
                if ($val->remoteShopSellerId == 44) {
                    $sellerShopName = 'Pickyshop';
                } else {
                    $sellerShopName = $shopFind->name;
                }
            }
            if ($perm) {
                $paid = ($paidAmount) ? 'Pagato' : 'Non Pagato';
                $row["id"] = '<a href="' . $opera . $val->id . '">H-' . $val->id . '</a><br/><b>' . $orderParal . '</b><br/><b><span style=\'color:' . $colorStatus[$val->status] . '\'>' . $val->orderStatus->orderStatusTranslation->getFirst()->title . '</span></b><br/><b>' . $paid . '<b><br />';
                if ($alert) $row["id"] .= " <i style=\"color:red\"class=\"fa fa-exclamation-triangle\"></i>";
            } else {
                $row["id"] = $val->id;
            }

                if ($supplier == null) {
                    $row["orderParal"] = $orderParal;
                } else {
                    $row["orderParal"] =  $orderParal.'<br><i style="color: fuchsia;
    font-size: 12px;
    display: inline-block;
    border: black;
    border-style: solid;
    border-width: 1.2px;
    padding: 0.1em;
    margin-top: 0.5em;
    padding-right: 4px;
    padding-left: 4px;"><b>Supplier:' . $supplier . '</b></i><br><b style="color: green;
    font-size: 12px;
    display: inline-block;
    border: black;
    border-style: solid;
    border-width: 1.2px;
    padding: 0.1em;
    margin-top: 0.5em;
    padding-right: 4px;
    padding-left: 4px;"><b>Seller:' . $sellerShopName . '</b></i>';
                }


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

                $row['user'] .= '<br />' . $val->billingAddress->country->name;
                $row['user'] .= '<br><button onclick="openTrackEmail(\'' . $val->id . '\');" class="btn btn-light" role="button"><i class="fa fa-envelope" aria-hidden="true"></i> Comunicazioni</button>';
            } catch (\Throwable $e) {

            }

            $row['user'] .= '<br/><b>' . $sellerShopName . ' - ' . $val->remoteOrderSellerId . '</b> ';


            $row["status"] = "<span style='color:" . $colorStatus[$val->status] . "'>" . $val->orderStatus->orderStatusTranslation->getFirst()->title . "</span>";

            $netTotal = SPriceToolbox::formatToEur($val->netTotal);
            $row["dareavere"] = (($val->netTotal !== $paidAmount) && ($val->orderPaymentMethodId !== 5)) ? "<span style='color:#FF0000'>" . $netTotal . "</span>" : $netTotal;
            if ($val->paymentDate == null) {
                $paymentDate = '';
            } else {
                $pamentDate = $val->paymentDate;
            }
            $row["paymentDate"] = $val->paymentDate;
            $row["payment"] = $val->orderPaymentMethod->name;
            $row["notes"] = wordwrap($val->note,50,'</br>');
            $userDetails = $val->user->userDetails;
            $note = ($userDetails) ? wordwrap($val->user->userDetails->note,50,'</br>') : '-';
            $row["userNote"] = $note;
            $row["orderSources"] = [];
            foreach ($val->campaignVisitHasOrder as $campaignVisitHasOrder) {
                $row["orderSources"][] = $campaignVisitHasOrder->campaignVisit->campaign->name . ' - ' . $campaignVisitHasOrder->campaignVisit->timestamp . ' - ' . $campaignVisitHasOrder->campaignVisit->cost . '€';
            }
            $row["orderSources"] = implode(',<br>',$row["orderSources"]);

            $findInvoiceSeller = $invoiceRepo->findBy(['orderId' => $val->id,'invoiceShopId' => $val->remoteShopSellerId]);
            if($val->remoteShopSellerId==44){
                $row["invoice"] = "<b>Pickyshop->Customer:       </b>";
            }else{
                $row["invoice"] = "<b>Seller->Customer:       </b>";
            }
            if ($findInvoiceSeller != null) {
                foreach ($findInvoiceSeller as $invoiceSeller) {
                    $shops = $shopRepo->findOneBy(['id' => $invoiceSeller->invoiceShopId]);
                    $shopInvoiceName = '(' . $shops->title . ')';
                    $row["invoice"] .= $shopInvoiceName . "<a target='_blank' href='/blueseal/xhr/InvoiceOnlyPrintAjaxController?orderId=" . $invoiceSeller->id . "&invoiceShopId=" . $invoiceSeller->invoiceShopId . "'>" . $invoiceSeller->invoiceNumber . "/" . $invoiceSeller->invoiceType . "</a><br />";
                }
            } else {
                $row["invoice"] .= "<br/>";
            }

            $findInvoiceSupplier = $invoiceRepo->findBy(['orderId' => $val->id]);

            if ($findInvoiceSupplier != null) {
                foreach ($findInvoiceSupplier as $invoicesSupplier) {
                    if ($invoicesSupplier->invoiceShopId != $val->remoteShopSellerId && $invoicesSupplier->invoiceShopId != 44) {
                        $row['invoice'] .= "</br><b>Supplier->Iwes:     </b>";
                        $shops = $shopRepo->findOneBy(['id' => $invoicesSupplier->invoiceShopId]);
                        if ($shops !== null) {
                            $shopInvoiceName = '(' . $shops->title . ')';
                        } else {
                            $shopInvoiceName = '';
                        }
                        $row["invoice"] .= $shopInvoiceName . "<a target='_blank' href='/blueseal/xhr/InvoiceOnlyPrintAjaxController?orderId=" . $invoicesSupplier->id . "&invoiceShopId=" . $invoicesSupplier->invoiceShopId . "'>" . $invoicesSupplier->invoiceNumber . "/" . $invoicesSupplier->invoiceType . "</a><br />";
                    }
                }
            } else {
                $row['invoice'] .= "<br>";
            }
            $findInvoiceToSeller = $invoiceRepo->findOneBy(['orderId' => $val->id,'invoiceShopId' => 44]);
            if($val->remoteShopSellerId!=44) {
                $row['invoice'] .= "<br/><b>Iwes->Seller: </b>";
                if ($findInvoiceToSeller != null) {
                    $row['invoice'] .= "<a target='_blank' href='/blueseal/xhr/InvoiceOnlyPrintAjaxController?orderId=" . $findInvoiceToSeller->id . "&invoiceShopId=44'>" . $findInvoiceToSeller->invoiceNumber . "/" . $findInvoiceToSeller->invoiceType . "</a><br />";
                } else {
                    $row['invoice'] .= "<br />";
                }
            }

            //  $row['shipmentId']=$val->shipmentId;
            /** Get doc */
            $fileName = "";
            /** @var CInvoiceDocument $iD */
            foreach ($val->invoiceDocument as $iD) {
                $fileName .= "<a target='_blank' href='/blueseal/download-customer-documents/" . $iD->id . "'>" . $iD->fileName . "</a></br>";
            }

            $row["documents"] = $fileName;

            $addressOrder = '';
            $address = CUserAddress::defrost($val->frozenShippingAddress);
            $address = $address != false ? $address : CUserAddress::defrost($val->frozenBillingAddress);
            $tableAddress = $val->user->userAddress->findOneByKey('id',$address->id);

            $country = $countryR->findOneBy(['id' => $address->countryId]);
            if ($country != null) {
                $countryName = $country->name;

            } else {
                $countryName = '';
            }
            $phone = is_null($address->phone) ? '---' : $address->phone;
            $addressOrder .= "
             <span><strong>Destinatario: </strong>$address->name $address->surname</span><br>
             <span><strong>Indirizzo: </strong>$address->address</span><br>
             <span><strong>CAP: </strong>$address->postcode</span><br>
             <span><strong>Città: </strong>$address->city</span><br>
             <span><strong>Provincia: </strong>$address->province</span><br>
             <span><strong>Paese:</strong>";
            $addressOrder .= $countryName . "</span><br>
             <span><strong>Telefono: </strong>$phone</span><br>";

            $row["address"] = $addressOrder;

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
        $eloyoR = \Monkey::app()->repoFactory->create('EloyVoucher');

        $dba = \Monkey::app()->dbAdapter;
        $orderRepo = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
        $shopId = $orderRepo->remoteShopSellerId;
        if ($shopId == null) {
            $shopId = 44;
        }
        $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
        $db_host = $shopRepo->dbHost;
        $db_name = $shopRepo->dbName;
        $db_user = $shopRepo->dbUsername;
        $db_pass = $shopRepo->dbPassword;
        try {

            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res = ' connessione ok <br>';
        } catch (PDOException $e) {
            $res = $e->getMessage();
        }
        if (ENV == 'prod') {
            $stmtOrder = $db_con->prepare("UPDATE `Order` SET `status`='" . $orderRepo->status . "' WHERE id=" . $orderRepo->remoteOrderSellerId);
            $stmtOrder->execute();
            $orderLineCancel = \Monkey::app()->repoFactory->create('OrderLine')->findOneBy(['orderId' => $orderId]);
            foreach ($orderLineCancel as $orlc) {
                $remoteIdOrderLine = $orlc->remoteOrderLineSellerId;
                $remoteStatusOrderLine = $orlc->status;
                $remoteOrderId = $orlc->remoteOrderSellerId;
                $stmtOrderLine = $db_con->prepare("UPDATE OrderLine SET `status`='" . $remoteStatusOrderLine . "' WHERE id=" . $remoteIdOrderLine . " and orderId=" . $remoteOrderId);
                $stmtOrderLine->execute();
            }
        }


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

                    if (!array_key_exists($ol->productId . '-' . $ol->productVariantId . '-' . $ol->productSizeId . '-' . $ol->shopId,$qtyToRestore)) {
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
                        $res = $dba->query($query,[
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

                    $logolz = $logR->findBy(['stringId' => $ol->printId(),'entityName' => 'OrderLine']);
                    foreach ($logolz as $logol) {
                        $logol->delete();
                    }
                    $ol->delete();
                }

                $cvho = $cvhoR->findBy(['orderId' => $order->id]);

                foreach ($cvho as $cvhoSingle) {
                    $cvhoSingle->delete();
                }


                $logOrderz = $logR->findBy(['stringId' => $orderId,'entityName' => 'Order']);
                foreach ($logOrderz as $logOrd) {
                    $logOrd->delete();
                }
                $EloyVoucherR = $eloyoR->findBy(['stringId' => $orderId,'entityName' => 'EloyVoucher']);
                if ($EloyVoucherR1 == null) {
                    foreach ($EloyVoucherR as $eloyV) {
                        $eloyV->delete();
                    }
                }
                $orderRepo = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
                $shopId = $orderRepo->remoteShopSellerId;
                if ($shopId == null) {
                    $shopId = 44;
                }
                $shopRepo = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $shopId]);
                $db_host = $shopRepo->dbHost;
                $db_name = $shopRepo->dbName;
                $db_user = $shopRepo->dbUsername;
                $db_pass = $shopRepo->dbPassword;
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }
                if (ENV === 'prod') {
                    $stmtOrder = $db_con->prepare("UPDATE `Order` SET `status`='" . $orderRepo->status . "' WHERE id=" . $orderRepo->remoteOrderSellerId);
                    $stmtOrder->execute();
                    $orderLineCancel = \Monkey::app()->repoFactory->create('OrderLine')->findOneBy(['orderId' => $orderId]);
                    foreach ($orderLineCancel as $orlc) {
                        $remoteIdOrderLine = $orlc->remoteOrderLineSellerId;
                        $remoteStatusOrderLine = $orlc->status;
                        $remoteOrderId = $orlc->remoteOrderSellerId;
                        $stmtOrderLine = $db_con->prepare("UPDATE OrderLine SET `status`='" . $remoteStatusOrderLine . "' WHERE id=" . $remoteIdOrderLine . " and orderId=" . $remoteOrderId);
                        $stmtOrderLine->execute();
                    }
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
            ['column' => 'o.creationDate','dir' => 'desc']
        ];

        $sqlOrder = " ORDER BY ";
        foreach ($dtOrderingColumns as $column) {
            if (isset($dbOrderingColumns[$column['column']]) && $dbOrderingColumns[$column['column']]['column'] !== null) {
                $sqlOrder .= $dbOrderingColumns[$column['column']]['column'] . " " . $column['dir'] . ", ";
            }
        }
        if (substr($sqlOrder,-1,2) != ', ') {
            foreach ($dbOrderingDefault as $column) {
                $sqlOrder .= $column['column'] . ' ' . $column['dir'] . ', ';
            }
        }
        return rtrim($sqlOrder,', ');
    }
}