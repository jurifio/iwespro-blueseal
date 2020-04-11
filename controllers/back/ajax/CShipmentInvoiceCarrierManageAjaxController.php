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
 * Class CShipmentInvoiceCarrierManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/04/2020
 * @since 1.0
 */
class CShipmentInvoiceCarrierManageAjaxController extends AAjaxController
{
    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $shipmentInvoiceNumber = $data['invoice'];
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $orderLineHasShipmentRepo = \Monkey::app()->repoFactory->create('OrderLineHasShipment');
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $res = '';
        $shipments = $shipmentRepo->findBy(['shipmentInvoiceNumber' => $shipmentInvoiceNumber]);
        try {
            foreach ($shipments as $shipment) {
                $orderLineHasShipment = $orderLineHasShipmentRepo->findBy(['shipmentId' => $shipment->id]);
                foreach ($orderLineHasShipment as $olhs) {
                    $orderLine = $orderLineRepo->findBy(['id' => $olhs->orderLineId,'orderId' => $olhs->orderId]);
                    foreach ($orderLine as $orl) {
                        $orl->isBillDeliveryCost = 1;
                        $orl->update();
                    }
                }

            }
            $res='Documento '.$shipmentInvoiceNumber. ' marcato come rifatturato';
        } catch (\Throwable $e){
            \Monkey::app()->applicationLog('CShipmentInvoiceCarrierManageAjaxController','Error','Rebilling invoice Error',$e,'');

        }

        return $res;
    }
}