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
        $shipment = $this->app->repoFactory->create('Shipment')->findOneByStringId($shipmentId);

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
            $dba->beginTransaction();
            $lineStatus = null;
            $date = '';
            $shipmentData = $this->app->router->request()->getRequestData('shipment');
            $shipment = $this->app->repoFactory->create('Shipment')->findOneByStringId($shipmentData['id']);
            $shipment->bookingNumber = $shipmentData['bookingNumber'];
            $shipment->trackingNumber = $shipmentData['trackingNumber'];
            $shipment->predictedShipmentDate = !empty($shipmentData['predictedShipmentDate']) ? STimeToolbox::DbFormattedDateTime( $shipmentData['predictedShipmentDate']) : null;
            $shipment->predictedDeliveryDate = !empty($shipmentData['predictedDeliveryDate']) ? STimeToolbox::DbFormattedDateTime($shipmentData['predictedDeliveryDate']) : null;
            if (!$shipment->shipmentDate && !(empty($shipmentData['shipmentDate']))) {
                $shipment->shipmentDate = STimeToolbox::DbFormattedDateTime($shipmentData['shipmentDate']);

                /** @var COrderLineRepo $lR */
                $olR = \Monkey::app()->repoFactory->create('OrderLine');
                $orderLine = $shipment->orderLine;
                foreach ($orderLine as $v) {
                    $olR->updateStatus($v, 'ORD_FRND_ORDSNT', $shipment->shipmentDate);
                }
            }

            if (!$shipment->deliveryDate && !(empty($shipmentData['deliveryDate']))) {
                $shipment->deliveryDate = STimeToolbox::DbFormattedDateTime($shipmentData['deliveryDate']);
                $this->app->repoFactory->create('Shipment')->checkIn($shipment);
            }

            $shipment->note = $shipmentData['note'];
            $shipment->update();

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
     * @transaction
     */
    public function delete() {
        $shipmentId = \Monkey::app()->router->request()->getRequestData('shipmentId');
        $faultId = \Monkey::app()->router->request()->getRequestData('faultId');
        /** @var CShipmentRepo $sR */
        $sR = \Monkey::app()->repoFactory->create('Shipment');
        $sfR = \Monkey::app()->repoFactory->create('ShipmentFault');
        $dba = \Monkey::app()->dbAdapter;
        try {
            $dba->beginTransaction();
            /** @var CShipment $s */
            $s = $sR->findOne([$shipmentId]);
            /** @var CShipmentFault $sf */
            $sf = $sfR->findOne([$faultId]);
            $sR->cancel($s, $sf);
            $newShipment = STimeToolbox::GetNextWorkingDay(new \DateTime($s->predictedShipmentDate));
            $sR->newFriendShipmentToUs(
                $s->carrierId,
                $s->fromAddressBookId,
                '',
                STimeToolbox::DbFormattedDate($newShipment),
                $s->orderLine
            );
            $dba->commit();
            return 'Spedizione annullata';
        } catch (BambooShipmentException $e) {
            $dba->rollBack();
            $res = [
                'exception' => 'shipment',
                'message' => $e->getMessage()
            ];
            return json_encode($res);
        } catch (BambooException $e) {
            $dba->rollBack();
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