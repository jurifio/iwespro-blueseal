<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CShipment;
use bamboo\domain\entities\CShipmentFault;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;

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
class CShipmentManageController extends AAjaxController
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
        $dba = \Monkey::app()->dbAdapter;
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            $lineStatus = null;
            $shipmentData = $this->app->router->request()->getRequestData('shipment');
            /** @var CShipment $shipment */
            $shipment = \Monkey::app()->repoFactory->create('Shipment')->findOneByStringId($shipmentData['id']);
            $shipment->bookingNumber = $shipmentData['bookingNumber'];
            $shipment->trackingNumber = $shipmentData['trackingNumber'];
            if($shipmentData['carrierId']!=null) {
                $shipment->carrierId = $shipmentData['carrierId'];
            }
            $shipment->predictedShipmentDate = !empty($shipmentData['predictedShipmentDate']) ? STimeToolbox::DbFormattedDateTime($shipmentData['predictedShipmentDate']) : null;
            $shipment->predictedDeliveryDate = !empty($shipmentData['predictedDeliveryDate']) ? STimeToolbox::DbFormattedDateTime($shipmentData['predictedDeliveryDate']) : null;
            if (!$shipment->shipmentDate && !(empty($shipmentData['shipmentDate']))) {
                $shipment->shipmentDate = STimeToolbox::DbFormattedDateTime($shipmentData['shipmentDate']);
                // $shipment->confirmShipment(); FIXME non ha mai funzionato?
                if ($shipment->scope == CShipment::SCOPE_SUPPLIER_TO_USER) {
                    /** @var COrderLineRepo $lR */
                    $olR = \Monkey::app()->repoFactory->create('OrderLine');
                    $orderLine = $shipment->orderLine;
                    foreach ($orderLine as $v) {
                        $olR->updateStatus($v, 'ORD_SENT', $shipment->shipmentDate);
                    }
                }
            }

            if (!$shipment->deliveryDate && !(empty($shipmentData['deliveryDate']))) {
                $shipment->deliveryDate = STimeToolbox::DbFormattedDateTime($shipmentData['deliveryDate']);
                \Monkey::app()->repoFactory->create('Shipment')->checkIn($shipment);
            }

            $shipment->note = $shipmentData['note'];
            $shipment->update();

            \Monkey::app()->repoFactory->commit();
            return 'Stato della spedizione aggiornato';
        } catch (BambooOrderLineException $e) {
            \Monkey::app()->repoFactory->rollback();
            return $e->getMessage();
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }

    public function post()
    {
        $request = $this->app->router->request();
        $fromAddressBookId = $request->getRequestData('fromAddressId');
        $carrierId = $request->getRequestData('carrierId');
        $shippingDate = $request->getRequestData('shipmentDate');
        $bookingNumber = $request->getRequestData('bookingNumber');
        $bookingNumber = empty($bookingNumber) ? null : $bookingNumber;
        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $shipmentRepo->newFriendShipmentToUs($carrierId, $fromAddressBookId, $bookingNumber, $shippingDate, []);
        return true;
    }

    /**
     * @transaction
     */
    public function delete()
    {
        $shipmentId = \Monkey::app()->router->request()->getRequestData('shipmentId');
        $faultId = \Monkey::app()->router->request()->getRequestData('faultId');
        $recreateShipment = (bool) \Monkey::app()->router->request()->getRequestData('recreate');
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
            $shipmentRepo->cancel($shipment, $shipmentFault);

            if($recreateShipment) {
                \Monkey::app()->applicationReport(
                    'ShipmentManageController',
                    'Shipment Recreation',
                    'Recreating shipment for id: '.$shipment->printId().' to '.$newShipmentDate
                    );
                switch ($shipment->scope) {
                    case CShipment::SCOPE_SUPPLIER_TO_US: {

                        if(!$newShipmentDate) {
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
                    case CShipment::SCOPE_US_TO_USER: {
                        if ($newShipmentDate) {
                            $newShipment = new \DateTime($newShipmentDate);
                            $shipmentRepo->newOrderShipmentToClient(
                                $shipment->carrierId,
                                null,
                                STimeToolbox::DbFormattedDateTime($newShipment),
                                $shipment->orderLine->getFirst()->order
                            );
                        }

                    }
                        break;
                    case CShipment::SCOPE_SUPPLIER_TO_USER: {
                        if ($newShipmentDate) {
                            $newShipment = new \DateTime($newShipmentDate);
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
    private function isOrderLineActionLogged(COrderLine $orderLine, $orderLineStatus)
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