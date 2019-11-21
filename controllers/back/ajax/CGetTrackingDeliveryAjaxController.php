<?php

namespace bamboo\controllers\back\ajax;
use bamboo\business\carrier\ACarrierHandler;
use bamboo\business\carrier;
use bamboo\business\carrier\IImplementedPickUpHandler;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\domain\entities\CCarrier;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CShipment;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CGetTrackingDeliveryAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/11/2019
 * @since 1.0
 */
class CGetTrackingDeliveryAjaxController extends AAjaxController
{
    public function get()
    {
        $request = \Monkey::app()->router->request();

        $trackingNumber = $request->getRequestData('trackingNumber');
        $shipment=\Monkey::app()->repoFactory->create('Shipment')->findOneBy(['trackingNumber'=>$trackingNumber]);
        $shipmentId=$shipment->id;
        $orderLineHasShipmentRepo=\Monkey::app()->repoFactory->create('OrderLineHasShipment')->findOneBy(['shipmentId'=>$shipmentId]);
        $orderId=$orderLineHasShipmentRepo->orderId;
        $orderRepo=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id'=>$orderId]);
        $userShipping = \bamboo\domain\entities\CUserAddress::defrost($orderRepo->frozenShippingAddress);
        $track=carrier\CUPSHandler::getTracking($shipment);
        $trackLine=[];
        foreach ($track->TrackResponse->Shipment->Package->Activity as $activities) {
            if (!empty($activities->ActivityLocation->Address->City)) {
            array_push($trackLine,[
                'orderId'=>$orderId,
                'customer'=>$userShipping->name.' '.$userShipping->surname. '<br>'.$userShipping->address.'<br>'.$userShipping->postcode.' '.$userShipping->city.' '.$userShipping->province,
                'bookingNumber'=>$shipment->bookingNumber,
                'trackingNumber'=>$shipment->trackingNumber,
                'creationDate'=>$shipment->creatiodDate,
                'DateTime'=> $activities->Status->Date.''.$activities->Status->Time,
                'Description' => $activities->Status->Description,
                'City'=> $activities->ActivityLocation->Address->City,
                'CountryCode'=>$activities->ActivityLocation->Address->CountryCode,
                'shipmentDate'=>$shipment->shipmentDate,
                'predictedDeliveryDate'=>$shipment->predictedDeliveryDate,
                'deliveryDate'=>$shipment->deliveryDate
                ]);
            }



        return json_encode($trackLine);
    }


}