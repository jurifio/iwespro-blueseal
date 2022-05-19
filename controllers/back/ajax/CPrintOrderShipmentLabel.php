<?php

namespace bamboo\controllers\back\ajax;

/**
 * Class CGetPermissionsForUser
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CPrintOrderShipmentLabel extends AAjaxController
{
    public function get()
    {
        $shipmentId = $this->app->router->request()->getRequestData('shipmentId');
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $shipment = $shipmentRepo->findOneByStringId($shipmentId);
        return $shipment->printLabel();
    }
}