<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CCarrier;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\export\order\COrderExport;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CCloseShipmentDay
 * @package bamboo\blueseal\jobs
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
class CCloseShipmentDay extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->report('run','Start');
        $resoults = \Monkey::app()->dbAdapter->query(
            "SELECT
                      c.id,
                      group_concat(s.id) AS shipmentsId
                    FROM Shipment s
                      JOIN
                      Carrier c ON s.carrierId = c.id
                    WHERE
                      c.isActive = 1 AND
                      s.cancellationDate IS NULL AND
                      s.shipmentDate IS NULL AND
                      nullif(trim(s.trackingNumber), '') IS NOT NULL AND
                      date(predictedShipmentDate) = date(now())
                    GROUP BY c.id",[],true)->fetchAll();
        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $carrierRepo = \Monkey::app()->repoFactory->create('Carrier');
        $this->report('run','Shipment founds',$resoults);
        foreach ($resoults as $res) {
            /** @var CCarrier $carrier */
            $carrier = $carrierRepo->findOne([$res['id']]);
            $shipments = new CObjectCollection();
            foreach (explode(',',$res['shipmentsId']) as $shipmentId) {
                $shipments->add($shipmentRepo->findOne([$shipmentId]));
            }
            foreach ($shipmentRepo->closeShipmentsForCarrier($shipments,$carrier) as $shipment) {
                if($shipment->scope == CShipment::SCOPE_US_TO_USER) {
                    foreach ($shipment->order as $order) {
                        /** @var COrder $order */
                        $order->updateStatus('ORD_SHIPPED');
                        try {
                            $to = [$order->user->email];
                            $this->app->mailer->prepare('shipmentclient', 'no-reply', $to, [], [], ['order' => $order, 'orderId' => $order->id, 'shipment' => $shipment, 'lang' => $order->user->lang]);
                            $res = $this->app->mailer->send();
                        } catch (\Throwable $e) {
                            $this->error('Shipping Emails','Error while shipment sending mail to client',$e->getTraceAsString());
                        }
                    }
                }
            }
        }

        $this->report('run','done');
    }
}