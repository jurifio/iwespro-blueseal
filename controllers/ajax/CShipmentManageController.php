<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShipment;

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
        $shipmentData = $this->app->router->request()->getRequestData('shipment');
        $shipment = $this->app->repoFactory->create('Shipment')->findOneByStringId($shipmentData['id']);
        $shipment->bookingNumber = $shipmentData['bookingNumber'];
        $shipment->trackingNumber = $shipmentData['trackingNumber'];
        $shipment->note = $shipmentData['note'];
        $shipment->update();
        return 'Aggiornato';
    }
}