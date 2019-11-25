<?php

namespace bamboo\controllers\back\ajax;
use bamboo\business\carrier\ACarrierHandler;
use bamboo\business\carrier\IImplementedPickUpHandler;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\CCarrier;
use bamboo\business\carrier;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CShipment;
use DateTime;
use PDO;
use PDOException;
use Mailgun\Mailgun;

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
class CGetTrackingEmailAjaxController extends AAjaxController
{
    public function get()
    {
        if(ENV == 'dev') {
            require '/media/sf_sites/vendor/mailgun/vendor/autoload.php';
        }else{
            require '/home/shared/vendor/mailgun/vendor/autoload.php';
        }


        $request = \Monkey::app()->router->request();

        $orderId = $request->getRequestData('orderId');
        $orderId=trim($orderId);
        $orderRepo = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
        $emailRepo =\Monkey::app()->repoFactory->create('User')->findOneBy(['id'=>$orderRepo->userId]);
        $email =$emailRepo->email;
        $mgClient = new Mailgun('key-1d5fe7e72fab58615be0d245d90e9e56');
        $domain = 'iwes.pro';
        $queryString = array(
            'begin'        => 'Fri, 23 November 2019 09:00:00 -0000',
            'ascending'    => 'yes',
            'pretty'       => 'yes',
            'recipient'    => 'juri@iwes.it'
        );

# Make the call to the client.
        $result = $mgClient->get("$domain/events", $queryString);


        foreach ($result->http_response_body->items as $list ) {
            echo 'oraInvio:'.$list->timestamp.'<br>';
            if (!empty($list->envelope->sender)) {
                echo 'sender:'.$list->envelope->sender . '<br>';
            }
            if (!empty($list->envelope->targets)) {
                echo 'targets:'.$list->envelope->targets . '<br>';
            }
            if (!empty($list->message->headers->to)) {
                echo 'to:'.$list->message->headers->to . '<br>';
            }
            if (!empty($list->message->headers->from)) {
                echo 'from:'.$list->message->headers->from . '<br>';
            }
            if (!empty($list->message->headers->subject)) {
                echo 'oggetto:'.$list->message->headers->subject . '<br>';
            }

        }
        $track = json_decode($result);
        $trackLine = [];
        foreach ($track->TrackResponse->Shipment->Package->Activity as $activities) {
            if (!empty($activities->ActivityLocation->Address->City)) {
                if (!empty($activities->ActivityLocation->Address->CountryCode)) {
                    array_push($trackLine,[
                        'orderId' => $orderId,
                        'carrier' => $carrierName,
                        'customer' => $userShipping->name . ' ' . $userShipping->surname . '<br>' . $userShipping->address . '<br>' . $userShipping->postcode . ' ' . $userShipping->city . ' ' . $userShipping->province,
                        'bookingNumber' => $shipment->bookingNumber,
                        'trackingNumber' => $shipment->trackingNumber,
                        'creationDate' => $shipment->creationDate,
                        'DateTime' => date('d/m/y H:i:s',strtotime($activities->Date.$activities->Time)),
                        'Description' => $activities->Status->Description,
                        'City' => $activities->ActivityLocation->Address->City,
                        'CountryCode' => $activities->ActivityLocation->Address->CountryCode,
                        'shipmentDate' => $shipment->shipmentDate,
                        'predictedDeliveryDate' => $shipment->predictedDeliveryDate,
                        'deliveryDate' => $shipment->deliveryDate
                    ]);
                }
            }


        }

        return json_encode($trackLine);
    }
}