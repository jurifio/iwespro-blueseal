<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\COrderLineRepo;
use function Couchbase\defaultDecoder;

/**
 * Class CFriendShipmentCheckInController
 * @package bamboo\controllers\back\ajax
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
class CFriendShipmentCheckInController extends AAjaxController
{
    public function post() {
        $shipmentsId = \Monkey::app()->router->request()->getRequestData('shipmentsId');
        foreach($shipmentsId as $shipmentId) {
            $shipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy($shipmentId);
            if($shipment === null) continue;

            switch($shipment->scope) {
                case CShipment::SCOPE_SUPPLIER_TO_US: {
                        $ok = $this->checkInFriendShipment($shipment);
                    break;
                }
                default:
                    break;
            }

        }
    }

    public function checkInFriendShipment(CShipment $shipment) {
        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');

        foreach($shipment->orderLine as $orderLine) {
            /** @var COrderLine $orderLine */
            $orderLine->orderLineStatus->nextOrderLineStatus;
            $orderLineRepo->updateStatus($orderLine,'ORD_CHK_IN');

        }
    }
}