<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CSelectLastShipmentInvoiceAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/01/2020
 * @since 1.0
 */
class CSelectLastShipmentInvoiceAjaxController extends AAjaxController
{
    public function get()
    {
        $selectShipment=[];
        $shipment = $this -> app -> router -> request() -> getRequestData('shipment');
        $res = $this -> app -> dbAdapter -> query('SELECT shipmentInvoiceNumber as shipmentInvoiceNumber , dateInvoice as dateInvoice from Shipment where shipmentInvoiceNumber is not null 
and dateInvoice is not NULL  order by lastUpdate desc limit 1
        ', []) -> fetchAll();

        foreach ($res as $result) {
            $invoiceDate=strtotime($result['dateInvoice']);
        $dateInvoice=date('Y-m-d\TH:i', $invoiceDate);
            $selectShipment[] = ['shipmentInvoiceNumber' => $result['shipmentInvoiceNumber'], 'dateInvoice' => $dateInvoice];
        }

        return json_encode($selectShipment);
    }
}