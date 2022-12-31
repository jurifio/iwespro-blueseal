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
        $trackingNumber=trim($trackingNumber);
        $shipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['trackingNumber' => $trackingNumber]);
        $shipmentId = $shipment->id;
        $carrier=\Monkey::app()->repoFactory->create('Carrier')->findOneBy(['id'=>$shipment->carrierId]);
        $carrierName=$carrier->name;
        $orderLineHasShipmentRepo = \Monkey::app()->repoFactory->create('OrderLineHasShipment')->findOneBy(['shipmentId' => $shipmentId]);
        $orderId = $orderLineHasShipmentRepo->orderId;
        $orderRepo = \Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderId]);
        $userShipping = \bamboo\domain\entities\CUserAddress::defrost($orderRepo->frozenShippingAddress);
        $trackingRequest = [
            'UPSSecurity' => [
                'UsernameToken' => [
                    'Username' => 'iwes123',
                    'Password' => 'Spedizioni123',
                ],
                "ServiceAccessToken" => [
                    'AccessLicenseNumber' =>
                        'ED3442CCB18DBE8C'
                ]
            ],
            'TrackRequest' => [
                'Request' => [
                    'RequestOption' => '1',
                    'TransactionReference' => [
                        'CustomerContext' => 'Richiesta Tracking Numero Ordine'
                    ]
                ],

                'InquiryNumber' => $trackingNumber

            ]
        ];

        $ch = curl_init();

//set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL,'https://wwwcie.ups.com/rest/Track');
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($trackingRequest));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);

        $result = curl_exec($ch);
        $e = curl_error($ch);

        curl_close($ch);
        $track = json_decode($result);
        $trackLine = [];
        foreach ($track->TrackResponse->Shipment->Package->Activity as $activities) {
            if (!empty($activities->ActivityLocation->Address->City)) {
                if (!empty($activities->ActivityLocation->Address->CountryCode)) {
                    $trackLine[] = [
                        'orderId' => $orderId,
                        'carrier' => $carrierName,
                        'customer' => $userShipping->name . ' ' . $userShipping->surname . '<br>' . $userShipping->address . '<br>' . $userShipping->postcode . ' ' . $userShipping->city . ' ' . $userShipping->province,
                        'bookingNumber' => $shipment->bookingNumber,
                        'trackingNumber' => $shipment->trackingNumber,
                        'creationDate' => $shipment->creationDate,
                        'DateTime' => date('d/m/y H:i:s',strtotime($activities->Date . $activities->Time)),
                        'Description' => $activities->Status->Description,
                        'City' => $activities->ActivityLocation->Address->City,
                        'CountryCode' => $activities->ActivityLocation->Address->CountryCode,
                        'shipmentDate' => $shipment->shipmentDate,
                        'predictedDeliveryDate' => $shipment->predictedDeliveryDate,
                        'deliveryDate' => $shipment->deliveryDate
                    ];
                }
            }


        }

        return json_encode($trackLine);
    }
}