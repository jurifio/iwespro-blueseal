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
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;
use PDO;
use PDOException;

/**
 * Class CShipmentDeleteAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/12/2019
 * @since 1.0
 */
class CShipmentDeleteAjaxController extends AAjaxController
{
    /**
     * @return string
     */

    /**
     * @transaction
     */
    public function delete()
    {
        $shipmentId = \Monkey::app()->router->request()->getRequestData('shipmentId');
        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $orderLineHasShipmentRepo = \Monkey::app()->repoFactory->create('OrderLineHasShipment');

        $orderLineDelete = $orderLineHasShipmentRepo->findBy(['shipmentId' => $shipmentId]);
        try {
            foreach ($orderLineDelete as $orlhs) {
                $orlhs->delete();
            }
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CShipmentDeleteAjaxController','error','cannot delete OrderLinesHasShipment',$e,'');
        }
        try {
            $shipmentDelete = $$shipmentRepo->findOneBy(['id' => $shipmentId]);
            $remoteShipmentId = $shipmentDelete->remoteShipmentId;
            $remoteShopShipmentId = $shipmentDelete->remoteShopShipmentId;
            if ($remoteShopShipmentId != 44) {
                if ($remoteShipmentId != null) {
                    $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $remoteShopShipmentId]);
                    $db_host = $shop->dbHost;
                    $db_name = $shop->dbName;
                    $db_user = $shop->dbUsername;
                    $db_pass = $shop->dbPassword;
                    try {

                        $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                        $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                        $res = " connessione ok <br>";
                    } catch (PDOException $e) {
                        $res = $e->getMessage();
                    }
                    try {
                        $stmtDeleteOrderLineHasShipment = $db_con->prepare('delete from  `OrderLineHasShipment` where  `shipmentId`=\'' . $remoteShipmentId . '\'');
                        $stmtDeleteOrderLineHasShipment->execute();
                    } catch (\Throwable $e) {
                        \Monkey::app()->applicationLog('CShipmentDeleteAjaxController','error','cannot delete remote OrderLinesHasShipment',$e,'');

                    }
                    try {
                        $stmtDeleteShipment = $db_con->prepare('delete from  `Shipment` where  `id`=\'' . $remoteShipmentId . '\'');
                        $stmtDeleteShipment->execute();
                    } catch (\Throwable $e) {
                        \Monkey::app()->applicationLog('CShipmentDeleteAjaxController','error','cannot delete remote Shipment',$e,'');
                    }

                }
            }
            $shipmentDelete->delete();
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CShipmentDeleteAjaxController','error','cannot delete Shipment',$e,'');
        }
        $res = 'La Spedizione è Stata Cancellata Definitivamente';
        return $res;
    }
}