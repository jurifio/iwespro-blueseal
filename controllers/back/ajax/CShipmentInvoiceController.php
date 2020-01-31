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

/**
 * Class CShipmentManageController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/12/2017
 * @since 1.0
 */
class CShipmentInvoiceController extends AAjaxController
{
    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $idShipment = $data['idShipment'];
        $shipmentInvoiceNumber = $data['shipmentInvoiceNumber'];
        $realShipmentPrice = $data['realShipmentPrice'];
        $trackingNumber = $data['trackingNumber'];
        $invoiceDate =strtotime($data['invoiceDate']);
        $invoiceDate=date('Y-m-d H:i:s', $invoiceDate);
        $dateNow=(new \DateTime())->format('Y-m-d H:i:s');
        $isBilling =$data['isBilling'];

        if (empty($shipmentInvoiceNumber) || empty($realShipmentPrice)){
            $res = "Non hai inserito le informazioni di fatturazizone in merito alla spedizione";
            return $res;
        } else {
            /** @var CShipmentRepo $shipmentRepo */
            $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');

            /** @var CShipment $shipment */
            $shipment =  $shipmentRepo->findOneBy(['id' => $idShipment ]);


            $shipment->shipmentInvoiceNumber = $shipmentInvoiceNumber;
            $shipment->realShipmentPrice = $realShipmentPrice;
            $shipment->dateInvoice =$invoiceDate;
            $shipment->isBilling =$isBilling;
            $shipment->lastUpdate=$dateNow;
            $shipment->update();
            $res = "Sunto della spedizione:</br>"."Numero fattura: <strong>".$shipmentInvoiceNumber."/".$invoiceDate."</strong></br>".
                "Costo reale di spedizione: <strong>".$realShipmentPrice."</strong></br>"."Tracking Number: <strong>".$trackingNumber."</strong>";
            return $res;
        }
    }


}