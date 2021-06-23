<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CCarrier;
use bamboo\domain\entities\CShipment;
use bamboo\domain\entities\CShipmentFault;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CShipmentManageController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CShipmentOrderManageController extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $shipmentId = $this->app->router->request()->getRequestData('shipmentId');
        $shipment = \Monkey::app()->repoFactory->create('Shipment')->findOneByStringId($shipmentId);

        $shipment->fromAddress;
        $shipment->toAddress;
        $shipment->carrier;
        $shipment->orderLine;
        $shipment->shipmentFaultId;
        return json_encode($shipment);
    }

    /**
     * @return string
     */
    public function put()
    {
        $request = $this->app->router->request();
        $fromAddressBookId = $request->getRequestData('fromAddress');
        $carrierId = $request->getRequestData('carrierId');
        $shippingDate = $request->getRequestData('shipmentDate');
        $orderId = $request->getRequestData('orderId');
        $orderLineId = $request->getRequestData('orderLineId');
        $bookingNumber = $request->getRequestData('bookingNumber');

        /** @var CCarrierRepo $carrierRepo */
        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $carrierRepo = \Monkey::app()->repoFactory->create('Carrier');
        $carrier = $carrierRepo->findOneBy(['id' => $carrierId]);
        $time = $carrier->prenotationTimeLimit;
        $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
        $remoteShopSellerId = $order->remoteShopSellerId;
        $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $remoteShopSellerId]);
        $db_host = $shop->dbHost;
        $db_name = $shop->dbName;
        $db_user = $shop->dbUsername;
        $db_pass = $shop->dbPassword;
        $hasEcommerce = 0;
        if ($shop->hasEcommerce == 1) {
            $hasEcommerce = 1;
        }
        $orderLineHasShipment=\Monkey::app()->repoFactory->create('OrderLineHasShipment')->findOneBy(['orderId'=>$orderId,'orderLineId'=>$orderLineId]);
        if($orderLineHasShipment){
            $orderLineHasShipment->delete();
        }

        if ($shipmentRepo->newOrderShipmentFromSupplierToClientSingleLine($carrierId,$fromAddressBookId,$bookingNumber,$shippingDate,$orderLineId,$orderId)) {
            $sql = 'select shipmentId from OrderLineHasShipment where orderId=' . $orderId . '  and orderLineId=' . $orderLineId;
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($res as $result) {
                $shipmentId = $result['shipmentId'];
            }

            if ($hasEcommerce == 1) {
                $newShipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id' => $shipmentId]);
                $newShipment->remoteShopShipmentId = $remoteShopSellerId;
                $newShipment->update();
                $findShipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id' => $shipmentId]);
                $remoteShopShipmentId=$remoteShopSellerId;
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }
                $stmRemoteOrder = $db_con->prepare("select frozenShippingAddress from `Order` where id=" . $remoteOrdeSellerId);
                $stmRemoteOrder->execute();
                while ($rowRemoteOrder = $stmRemoteOrder->fetch(PDO::FETCH_ASSOC)) {
                    $frozenShippingAddress = json_decode($rowRemoteOrder['frozenShippingAddress'],true);
                }
                $toAddressBookId = $frozenShippingAddress->id;
                $stmtRemoteShop = $db_con->prepare("select billingAddressBookId from Shop where id=" . $remoteShopSellerId);
                $stmtRemoteShop->execute();
                while ($rowRemoteShop = $stmRemoteShop->fetch(PDO::FETCH_ASSOC)) {
                    $fromAddressBookId = json_decode($rowRemoteOrder['billingAddressBookId'],true);
                }

                $stmtShipmentInsert = $db_con->prepare("Insert INTO  Shipment (`carrierId`,`scope`,`bookingNumber`,`trackingNumber`,`predictedShipmentDate`
                ,`shipmentDate`,
                `predictedDeliveryDate`,
                `deliveryDate`,
                `creationDate`,
                `declaredValue`,
                `fromAddressBookId`,
                `toAddressBookId`,
                `note`,
                `cancellationDate`,
                `shipmentInvoiceNumber`,
                `realShipmentPrice`,
                `trackingShipmentStatus`) 
                       values(
                              '" . $carrierId . "',
                               '" . $findShipment->scope . "',
                              '" . $findShipment->bookingNumber . "',
                               '" . $findShipment->trackingNumber . "',
                               '" . $findShipment->predictedShipmentDate . "',
                              '" . $findShipment->shipmentDate . "',
                               '" . $findShipment->predictedDeliveryDate . "',
                               '" . $findShipment->deliveryDate . "',
                                '" . $findShipment->creationDate . "',
                                   '" . $findShipment->declaredValue . "',
                                      '" . $fromAddressBookId . "',
                                         '" . $toAddressBookId . "',
                                           '" . $findShipment->note . "',
                                            '" . $findShipment->cancellationDate . "',
                                             '" . $findShipment->shipmentFaultId . "',
                                              '" . $findShipment->note . "',
                                               '" . $findShipment->shipmentInvoiceNumber . "',
                                                '" . $findShipment->realShipmentPrice . "',
                                                 '" . $findShipment->trackingShipmentStatus . "'
                       )");
                $stmtShipmentInsert->execute();
                $remoteShipmentId = $stmtShipmentInsert->lastInsertId();
                $findShipment->remoteShipmentId = $remoteShipmentId;
                $findShipment->update();

            }
            return $shipmentId;
        } else {
            return 'C\'è stato un problema';
        }


    }


    public function post()
    {
        $request = $this->app->router->request();
        $fromAddressBookId = $request->getRequestData('fromAddressId');
        $carrierId = $request->getRequestData('carrierId');
        $shippingDate = $request->getRequestData('shipmentDate');
        $orderId = $request->getRequestData('orderId');
        $orderLineId = $request->getRequestData('orderLineId');
        $trackingNumber = $request->getRequestData('trackingNumber');

        /** @var CCarrierRepo $carrierRepo */
        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $carrierRepo = \Monkey::app()->repoFactory->create('Carrier');
        $carrier = $carrierRepo->findOneBy(['id' => $carrierId]);
        $time = $carrier->prenotationTimeLimit;
        $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
        $orderLine=\Monkey::app()->repoFactory->create('OrderLine')->findOneBy(['id'=>$orderLineId,'orderId' => $orderId]);
        $remoteShopSellerId = $order->remoteShopSellerId;
        $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $remoteShopSellerId]);
        $db_host = $shop->dbHost;
        $db_name = $shop->dbName;
        $db_user = $shop->dbUsername;
        $db_pass = $shop->dbPassword;
        $hasEcommerce = 0;
        if ($shop->hasEcommerce == 1) {
            $hasEcommerce = 1;
        }
        $orderLineHasShipment = \Monkey::app()->repoFactory->create('OrderLineHasShipment')->findOneBy(['orderId' => $orderId,'orderLineId' => $orderLineId]);
        $shipmentId = $orderLineHasShipment->shipmentId;
        $shipment = $shipmentRepo->findOneBy(['id' => $shipmentId]);
        $to = [$order->user->email];
        $lang=\Monkey::app()->repoFactory->create('Lang')->findOneBy(['id'=>$order->user->langId]);
        $urlSite=$shop->urlSite;
        $logoSite=$shop->logoSite;
        $noreply='no-reply@'.str_replace('https://www.','',$urlSite);
        /** @var CEmailRepo $emailRepo */
        $emailRepo = \Monkey::app()->repoFactory->create('Email');

        $emailRepo->newPackagedMail('shipmentclient',$noreply.$urlSite, $to,[],[],
            ['order'=>$order,'orderId'=>$orderId,'shipment'=>$shipment,'lang'=>$lang->lang,'logoSite'=>$logoSite,'urlSite'=>$shop->urlSite],'mailGun',null);
        $remoteShipmentId = $shipment->remoteShipmentId;
        $remoteShopShipmentId = $shipment->remoteShopShipmentId;
        if ($carrier->implementation!='' && $trackingNumber == '') {
            // creo chiamata api corriere e creo spedizione
            $orderLineHasShipment->delete();
            $shipmentRepo->newOrderShipmentToClientSingleLine($carrierId,$trackingNumber,$shippingDate,$order,$orderLineId);
            $sql = 'select shipmentId from OderLineHasShipment where orderId=' . $orderId . '  and orderLineId=' . $orderlineId;
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            foreach ($res as $result) {
                $shipmentId = $result['shipmentId'];
            }
            if ($hasEcommerce == 1) {
                $newShipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id' => $shipmentId]);
                $newShipment->remoteShipmentId = $remoteShipmentId;
                $trackingNumber = $newShipment->trackingNumber;
                $newShipment->remoteShopShipmentId = $remoteShopShipmentId;
                $newShipment->update();
                $newShipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id' => $shipmentId]);
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }
                $stmtShipmentInsert = $db_con->prepare("Insert INTO  Shipment (`carrierId`,`scope`,`bookingNumber`,`trackingNumber`,`predictedShipmentDate`
                ,`shipmentDate`,`predictedDeliveryDate`,`deliveryDate`,`creationDate`,`declaredValue`,`fromAddressBookId`,`toAddressBookId`,`note`,``) set carrierId=" . $carrierId . ",
                                                                           trackingNumber='" . $trackingNumber . "',
                                                                           shipmentDate='" . (new \DateTime($shippingDate))->format('Y-m-d H:i:s') . "'
                                                                           where id=" . $remoteShipmentId);
                $stmtShipmentInsert->execute();
            }


            // controllo se l'ordine è un ordine parallelo o è ecommerce e aggiorno i relatvi database
        } else {

            // aggiorno la spedizione con il nuovo tracking number
            $shipment->trackingNumber = $trackingNumber;
            $shipment->shipmentDate = STimeToolbox::DbFormattedDateTime($shippingDate);
            $shipment->fromAddressBookId = $fromAddressBookId;
            $shipment->update();

            // controllo se l'orinde è un ordine parallelo o è ecommerce e aggiorno i relativi database


        }

        if ($hasEcommerce == 1) {

            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                $res = ' connessione ok <br>';
            } catch (PDOException $e) {
                $res = $e->getMessage();
            }
            try{
                $stmtOrderUpdate=$db_con->prepare("Update OrderLine  set `status`='ORD_SENT'
                                                                        
                                                                           where orderId=" . $orderLine->remoteOrderSellerId . " and  id=".$orderLine->remoteOrderLineSellerId);
                $stmtOrderUpdate->execute();
            } catch (PDOException $e) {
                $res = $e->getMessage();
            }

            $stmtShipmentUpdate = $db_con->prepare("Update Shipment  set carrierId=" . $carrierId . ",
                                                                           trackingNumber='" . $trackingNumber . "',
                                                                           shipmentDate='" . STimeToolbox::DbFormattedDateTime($shippingDate) . "'
                                                                           where id=" . $remoteShipmentId);
            $stmtShipmentUpdate->execute();
        }




        return $shipmentId;
    }

    /**
     * @transaction
     */
    public function delete()
    {
        $shipmentId = \Monkey::app()->router->request()->getRequestData('shipmentId');
        $faultId = \Monkey::app()->router->request()->getRequestData('faultId');
        $recreateShipment = (bool)\Monkey::app()->router->request()->getRequestData('recreate');
        $newShipmentDate = \Monkey::app()->router->request()->getRequestData('newShipmentDate');
        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $shipmentFaultRepo = \Monkey::app()->repoFactory->create('ShipmentFault');
        $dba = \Monkey::app()->dbAdapter;
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            /** @var CShipment $shipment */
            $shipment = $shipmentRepo->findOne([$shipmentId]);
            /** @var CShipmentFault $shipmentFault */
            $shipmentFault = $shipmentFaultRepo->findOne([$faultId]);
            $shipmentRepo->cancel($shipment,$shipmentFault);

            if ($recreateShipment) {
                \Monkey::app()->applicationReport(
                    'ShipmentManageController',
                    'Shipment Recreation',
                    'Recreating shipment for id: ' . $shipment->printId() . ' to ' . $newShipmentDate
                );
                switch ($shipment->scope) {
                    case CShipment::SCOPE_SUPPLIER_TO_US:
                        {

                            if (!$newShipmentDate) {
                                $newShipmentDate = SDateToolbox::GetNextWorkingDay(STimeToolbox::GetDateTime());
                            }
                            $shipmentRepo->newFriendShipmentToUs(
                                $shipment->carrierId,
                                $shipment->fromAddressBookId,
                                '',
                                STimeToolbox::DbFormattedDate(date($newShipmentDate)),
                                $shipment->orderLine
                            );

                        }
                        break;
                    case CShipment::SCOPE_US_TO_USER:
                        {
                            if ($newShipmentDate) {
                                $newShipment = new DateTime($newShipmentDate);
                                $shipmentRepo->newOrderShipmentToClient(
                                    $shipment->carrierId,
                                    null,
                                    STimeToolbox::DbFormattedDateTime($newShipment),
                                    $shipment->orderLine->getFirst()->order
                                );
                            }

                        }
                        break;
                    case CShipment::SCOPE_SUPPLIER_TO_USER:
                        {
                            if ($newShipmentDate) {
                                $newShipment = new DateTime($newShipmentDate);
                                $shipmentRepo->newOrderShipmentFromSupplierToClient(
                                    $shipment->carrierId,
                                    null,
                                    STimeToolbox::DbFormattedDateTime($newShipment),
                                    $shipment->orderLine->getFirst()->order
                                );
                            }

                        }
                        break;
                }
            }

            \Monkey::app()->repoFactory->commit();
            return 'Spedizione annullata';
        } catch (BambooShipmentException $e) {
            \Monkey::app()->repoFactory->rollback();
            $res = [
                'exception' => 'shipment',
                'message' => $e->getMessage()
            ];
            return json_encode($res);
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            $res = [
                'exception' => 'general',
                'message' => $e->getMessage()
            ];
            return json_encode($res);
        }
    }

    /**
     * @param COrderLine $orderLine
     * @param string $shippedOrDelivered
     */
    private function isOrderLineActionLogged(COrderLine $orderLine,$orderLineStatus)
    {
        $lR = \Monkey::app()->repoFactory->create('Log');
        $log = $lR->findOneBy([
            'entityName' => 'OrderLine',
            'stringId' => $orderLine->printId(),
            'actionName' => 'OrderStatusLog',
            'eventValue' => $orderLineStatus
        ]);
        return $log;
    }
}