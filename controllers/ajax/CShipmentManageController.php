<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;

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
        $shipment = $this->app->repoFactory->create('Shipment')->findOneByStringId($shipmentId);

        $shipment->fromAddress;
        $shipment->toAddress;
        $shipment->carrier;
        $shipment->orderLine;
        return json_encode($shipment);
    }

    /**
     * @return string
     */
    public function put()
    {
        $dba = \Monkey::app()->dbAdapter;
        try {
            $dba->beginTransaction();
            $shipmentData = $this->app->router->request()->getRequestData('shipment');
            $shipment = $this->app->repoFactory->create('Shipment')->findOneByStringId($shipmentData['id']);
            $shipment->bookingNumber = $shipmentData['bookingNumber'];
            $shipment->trackingNumber = $shipmentData['trackingNumber'];
            if (!$shipment->shipmentDate && !(empty($shipmentData['shipmentDate']))) {
                $shipment->shipmentDate = $shipmentData['shipmentDate'];
            }
            if (!$shipment->deliveryDate && !(empty($shipmentData['deliveryDate']))) {
                $shipment->deliveryDate = $shipmentData['deliveryDate'];
            }
            $shipment->note = $shipmentData['note'];
            $shipment->update();

            $lineStatus = null;
            $date = null;

            if ($shipmentData['deliveryDate']) {
                if (!$shipment->shipmentDate)
                    throw new BambooException('Non può essere fatto il checkin di un prodotto che non risulta spedito');
                $lineStatus = 'ORD_CHK_IN';
                $date = $shipmentData['deliveryDate'];
            } elseif ($shipmentData['shipmentDate']) {
                $lineStatus = 'ORD_FRND_ORDSNT';
                $date = $shipmentData['shipmentDate'];
            }

            if ($lineStatus) {
                /** @var COrderLineRepo $lR */
                $olR = \Monkey::app()->repoFactory->create('OrderLine');
                $orderLine = $shipment->orderLine;
                foreach ($orderLine as $v) {
                    if (!$this->isOrderLineActionLogged($v, $lineStatus)) {
                        $olR->updateStatus($v, $lineStatus, $date);
                    }
                }
            }
            $dba->commit();
            return 'Stato della spedizione aggiornato';
        } catch(BambooOrderLineException $e) {
            $dba->rollBack();
            return $e->getMessage();
        } catch(BambooException $e) {
            $dba->rollBack();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }

    public function post() {
        $request = $this->app->router->request();
        $fromAddressBookId = $request->getRequestData('fromAddressId');
        $carrierId = $request->getRequestData('carrierId');
        $shippingDate = $request->getRequestData('shipmentDate');
        $bookingNumber = $request->getRequestData('bookingNumber');
        $bookingNumber = empty($bookingNumber) ? null : $bookingNumber;
        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = $this->app->repoFactory->create('Shipment');
        $shipmentRepo->newFriendShipmentToUs($carrierId,$fromAddressBookId,$bookingNumber,$shippingDate,[]);
        return true;
    }


    /**
     * @param COrderLine $orderLine
     * @param string $shippedOrDelivered
     */
    private function isOrderLineActionLogged(COrderLine $orderLine, $orderLineStatus) {
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