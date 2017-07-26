<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\COrder;
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
class CPrintOrderShipmentLabel extends AAjaxController
{
    public function get()
    {
        $shipmentId = $this->app->router->request()->getRequestData('shipmentId');
        $shipmentRepo = $this->app->repoFactory->create('Shipment');
        $shipment = $shipmentRepo->findOneByStringId($shipmentId);
        return $shipment->printLabel();
    }
}