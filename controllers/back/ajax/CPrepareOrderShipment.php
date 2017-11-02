<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\COrderRepo;
use bamboo\domain\repositories\CShipmentRepo;

/**
 * Class CGetPermissionsForUser
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CPrepareOrderShipment extends AAjaxController
{
    use TMySQLTimestamp;

    public function post()
    {
        $carrier = $this->app->router->request()->getRequestData('carrier');
        $ordersId = $this->app->router->request()->getRequestData('ordersId');
        $carrier = $this->app->repoFactory->create('Carrier')->findOneByStringId($carrier);

        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = $this->app->repoFactory->create('Shipment');
        /** @var COrderRepo $orderRepo */
        $orderRepo = $this->app->repoFactory->create('Order');

        $trackingNumber = $this->app->router->request()->getRequestData('tracking');

        $shipments = [];
        $orders = [];
        if ($carrier->implementation == null) {
            if (count($ordersId[0]) != 1) throw new BambooException('Non è possibile gestire più di un ordine per volta manualmente');

        }
        foreach ($ordersId as $orderId) {
            /** @var COrder $order */
            $order = $orderRepo->findOneByStringId($orderId);

            $exShipments = [];
            foreach ($order->orderLine as $orderLine) {
                foreach ($orderLine->shipment as $exShipment) {
                    if($exShipment->scope == CShipment::SCOPE_US_TO_USER) {
                        $exShipments[] = $exShipment;
                    }
                }
            }

            if(count($exShipments) == 0) {
                $shipment = $shipmentRepo->newOrderShipmentToClient($carrier->id, $trackingNumber, $this->time(), $order);

                $order->note = $order->note . " Tracking " . $shipment->carrier->name . ": " . $shipment->trackingNumber . ' Spedito: ' . date('Y-m-d');
                $order->update();
                $orderRepo->updateStatus($order,'ORD_PACK');

                $shipments[] = $shipment;
            } else {
                $shipments += $exShipments;
            }

            $orders[] = $order;
        }


        return json_encode(['shipments'=>$shipments,'orders'=>$orders]);
    }
}