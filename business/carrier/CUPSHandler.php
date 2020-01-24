<?php

namespace bamboo\business\carrier;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CUPSHandler
 * @package bamboo\business\carrier
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
class CUPSHandler extends ACarrierHandler implements IImplementedPickUpHandler
{

    protected $config = [
        'pickUpEndopoint' => 'https://onlinetools.ups.com/rest/Pickup',
        'shipEndpoint' => 'https://onlinetools.ups.com/rest/Ship',
        'voidPackageEndpoint' => 'https://onlinetools.ups.com/rest/Void',
        'labelRecoveryEndpoint' => 'https://onlinetools.ups.com/rest/LBRecovery',
        'timeInTransitEndpoint' => 'https://onlinetools.ups.com/rest/TimeInTransit',
        'trackRequest' => 'https://onlinetools.ups.com/rest/Track',
        'ServiceAccessToken' => 'ED3442CCB18DBE8C',
        'UPSClientCode' => '463V1V',
        'username' => 'iwes123',
        'password' => 'Spedizioni123'
    ];

    protected $testConfig = [
        'pickUpEndopoint' => 'https://wwwcie.ups.com/rest/Pickup',
        'shipEndpoint' => 'https://wwwcie.ups.com/rest/Ship',
        'voidPackageEndpoint' => 'https://wwwcie.ups.com/rest/Void',
        'labelRecoveryEndpoint' => 'https://wwwcie.ups.com/rest/LBRecovery',
        'timeInTransitEndpoint' => 'https://wwwcie.ups.com/rest/TimeInTransit',
        'trackRequest' => 'https://wwwcie.ups.com/rest/Track',
        'ServiceAccessToken' => 'ED3442CCB18DBE8C',
        'UPSClientCode' => '463V1V',
        'username' => 'iwes123',
        'password' => 'Spedizioni123'
    ];

    /**
     * @param CShipment $shipment
     * @param $orderId
     * @return CShipment|bool
     * $orderId
     * @throws BambooException
     * @throws \Throwable
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    public function addPickUp(CShipment $shipment,$orderId)
    {
        \Monkey::app()->applicationReport('CUPSHandler', 'addPickup', 'Called addPickUp');

        $shipment = $this->addDelivery($shipment,$orderId);

        $orders = [];

        foreach ($shipment->orderLine as $orderLine) {
            $orders[] = $orderLine->printId();
        }

        $delivery = [
            'UPSSecurity' => $this->getUpsSecurity(),
            'PickupCreationRequest' => [
                'SpecialInstruction' => substr(empty($shipment->fromAddress->note) ? 'Attività Commerciale' : $shipment->fromAddress->note,0,56),
                'Request' => [
                    'TransactionReference' => [
                        'CustomerContext' => 'Order '.implode(',',$orders) //???
                    ]
                ],
                'RatePickupIndicator' => 'Y',
                'TaxInformationIndicator' => 'Y',
                'PickupDateInfo' => [
                    'CloseTime' => '2000',
                    'ReadyTime' => '0930',
                    'PickupDate' => STimeToolbox::GetDateTime($shipment->predictedShipmentDate)->format('Ymd')
                ],
                'Shipper' => [
                    'Account' => [
                        'AccountNumber' => $this->getConfig('UPSClientCode'),
                        'AccountCountryCode' => 'IT'
                    ]
                ],
                'PickupAddress' => [
                    'CompanyName' => $shipment->fromAddress->subject,
                    'ContactName' => 'Anyone',
                    'AddressLine' => $shipment->fromAddress->address,
                    'City' => $shipment->fromAddress->city,
                    'PostalCode' => $shipment->fromAddress->postcode,
                    'CountryCode' => $shipment->fromAddress->country->ISO,
                    'ResidentialIndicator' => 'N',
                    'Phone' => [
                        'Number' => !empty($shipment->fromAddress->phone) ? $shipment->fromAddress->phone : ($shipment->fromAddress->cellphone ? $shipment->fromAddress->cellphone : '0733471365') //$shipment->fromAddress->phone ?? $shipment->fromAddress->cellphone
                    ]
                ],
                'AlternateAddressIndicator' => '', // mi sa che serve, se ognuno ha il suo account
                'PickupPiece' => [
                    'ServiceCode' => '011',
                    'Quantity' => '1',
                    'DestinationCountryCode' => $shipment->toAddress->country->ISO,
                    'ContainerCode' => '01'
                ],
                'OverweightIndicator' => 'N',
                'PaymentMethod' => '01',
                'TrackingData' => [
                    'TrackingNumber' => $shipment->trackingNumber
                ]
            ]
        ];
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->getConfig('pickUpEndopoint'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($delivery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);
        \Monkey::app()->applicationReport(
            'UPSHandler',
            'addPickup',
            'Request addPickup to' . $this->getConfig('pickUpEndopoint'),
            json_encode($delivery));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport(
            'UPSHandler',
            'addDelivery',
            'Result addPickup to ' . $this->getConfig()['pickUpEndopoint'],
            $result);
        try {
            $result = json_decode($result);
            try {
                $status = $result->PickupCreationResponse->Response->ResponseStatus->Code;
                if ($status == '1') {
                    $shipment->bookingNumber = $result->PickupCreationResponse->PRN;
                } else throw new BambooException('Status not 1');

                $shipment->update();
            } catch (\Throwable $e) {
                throw new BambooException('Failed to addPickup from UPS: ' . $result->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
            }

        } catch (\Throwable $e) {
            $this->cancelDelivery($shipment);
            \Monkey::app()->applicationWarning('UpsHandler', 'addPickUp', 'Error while parsing response of addPickUp for ' . $shipment->printId(), $e);
            throw $e;
        }
        return false;
    }

    /**
     * @return array
     */
    protected function getUpsSecurity()
    {
        return [
            'UsernameToken' => [
                'Username' => $this->getConfig('username'),
                'Password' => $this->getConfig('password')
            ],
            'ServiceAccessToken' => [
                "AccessLicenseNumber" => $this->getConfig('ServiceAccessToken')
            ]
        ];
    }

    /**
     * @param null $name
     * @return array|mixed
     */
    private function getConfig($name = null)
    {
        if(ENV == 'dev') {
            $config = $this->testConfig;
        } else {
            $config = $this->config;
        }
        if ($name) return $config[$name];
        return $config;
    }

    /**
     * @param CShipment $shipment
     * @param $orderId
     * @return CShipment
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    public function addDelivery(CShipment $shipment,$orderId)
    {
        \Monkey::app()->applicationReport('UpsHandler', 'addDelivery', 'Called addDelivery');
       $orderLineHasShipments=\Monkey::app()->repoFactory->create('OrderLineHasShipment')->findOneBy(['shipmentId'=>$shipment->id]);
        $valuePrice=0;
       if($orderLineHasShipments!=null) {
          // $orderId = $orderLineHasShipments->orderId;
           }
        foreach ($shipment->orderLine as $orderLine) {
            $orders[] = $orderLine->printId();
            $valuePrice+=$orderLine->netPrice;
          //  $orderId=$orderLine->orderId;
        }
       $findOrder=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id'=>$orderId]);

            if($findOrder->isShippingToIwes == null || $findOrder->isShippingToIwes == 0  ) {
                $shippingAddress[] = json_decode($findOrder->frozenBillingAddress,true);
                $AttentionName =  preg_replace('/[\,\.\"\'\/\&%#\$]/',' ',$shippingAddress[0]['name'] . ' ' . $shippingAddress[0]['surname']);
                $Name = preg_replace('/[\,\.\"\'\/\&%#\$]/',' ',$shippingAddress[0]['name'] . ' ' . $shippingAddress[0]['surname'].' '.$shippingAddress[0]['company']);
                $AddressLine = preg_replace('/[\,\.\"\'\/\&%#\$]/',' ', $shippingAddress[0]['address'] );
                $City = preg_replace('/[\,\.\"\'\/\&%#\$]/',' ',$shippingAddress[0]['city']);
                $PostalCode = $shippingAddress[0]['postcode'];
                $country = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $shippingAddress[0]['countryId']]);
                $CountryCode = $country->ISO;
                $Number = $shippingAddress[0]['phone'];
            }else{
                $AttentionName =  'Iwes Logistic';
                $Name = 'Iwes snc';
                $AddressLine = 'Via Cesare Pavese 1';
                $City = 'Civitanova Marche';
                $PostalCode = '62012';
                $country = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => '110']);
                $CountryCode = $country->ISO;
                $Number = '00390733471365';

            }

            $shipperName ='Iwes Snc';
            $shipperAttentionName= 'Iwes Logistic';
            $shipperAddressLine= 'Via Cesare Pavese 1';
            $shipperCity= 'Civitanova Marche';
            $shipperPostalCode= '62012';
            $shipperCountryCode= 'IT';



        $service = [
            'Code' => '11',
            'Description' => 'UPS Standard'
        ];

        if($findOrder->isShippingToIwes == null || $findOrder->isShippingToIwes == 0  ) {
            if ($shipment->toAddress->country->continent != 'EU') {
                $service = [
                    'Code' => '65',
                    'Description' => 'UPS Saver'
                ];
            }
        }
        if($findOrder->orderPaymentMethodId==5){

            $shipmentServiceOptions=[
                'COD'=>[
                    'CODAmount'=>[
                        'MonetaryValue'=>money_format('%.2n', $valuePrice),
                        'CurrencyCode'=> 'EUR'
                    ],
                    'CODFundsCode ' => '1',
                    'CODCode'=>'3'
                ]
            ];

        }else{
            $shipmentServiceOptions='';
        }

        $delivery = [
            'UPSSecurity' => $this->getUpsSecurity(),
            'ShipmentRequest' => [
                'Request' => [
                    'RequestOption' => 'validate',
                    'TransactionReference' => [
                        'CustomerContext' => 'CustomerContext.' //???
                    ]
                ],
                'Shipment' => [
                    'Description' => $shipment->printId(),
                    'Shipper' => [
                        'Name' => $shipperName,
                        'AttentionName' => $shipperAttentionName,
                        'ShipperNumber' => $this->getConfig()['UPSClientCode'],
                        'Address' => [
                            'AddressLine' => $shipperAddressLine,
                            'City' => $shipperCity,
                            'PostalCode' => $shipperPostalCode,
                            'CountryCode' => $shipperCountryCode
                        ],
                        'Phone' => [
                            'Number' => !empty($shipment->fromAddress->phone) ? $shipment->fromAddress->phone : ($shipment->fromAddress->cellphone ? $shipment->fromAddress->cellphone : '+390733471365') //$shipment->fromAddress->phone ?? $shipment->fromAddress->cellphone
                        ]
                    ],
                    'ShipFrom' => [
                        'Name' => preg_replace('/[\,\.\"\'\/\&%#\$]/',' ',$shipment->fromAddress->subject),
                        'Address' => [
                            'AddressLine' => preg_replace('/[\,\"\'\/\&%#\$]/',' ',$shipment->fromAddress->address) . ' ',
                            'City' => preg_replace('/[\,\"\'\/\&%#\$]/',' ',$shipment->fromAddress->city),
                            'PostalCode' => $shipment->fromAddress->postcode,
                            'CountryCode' => $shipment->fromAddress->country->ISO
                        ]
                    ],
                    'ShipTo' => [
                        'AttentionName' => $AttentionName,
                        'Name' => $Name,
                        'Address' => [
                            'AddressLine' => $AddressLine,
                            'City' => $City,
                            'PostalCode' => $PostalCode,
                            'CountryCode' => $CountryCode
                        ],
                        'Phone' => [
                            'Number' => $Number
                        ]
                    ],
                    'PaymentInformation' => [
                        'ShipmentCharge' => [
                            'Type' => '01',
                            'BillShipper' => [
                                'AccountNumber' => $this->getConfig()['UPSClientCode']
                            ]
                        ]
                    ],
                    'Service' => $service,
                    'ShipmentServiceOptions'=>$shipmentServiceOptions,
                    'Package' => [
                        'Description' => 'Scatola di Cartone',
                        'Packaging' => [
                            'Code' => '02',
                            'Description' => 'Customer Supplied'
                        ],
                        'Dimensions' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'CM'

                            ],
                            'Length' => '45',
                            'Width' => '35',
                            'Height' => '15'
                        ],
                        'PackageWeight' => [
                            'UnitOfMeasurement' => [
                                'Code' => 'KGS',
                                'Description' => 'Kilograms'
                            ],
                            'Weight' => '2.5'
                        ]
                    ]
                ]
            ]
        ];
        \Monkey::app()->applicationLog('CUPSHandler','report','string call',json_encode($delivery),'');
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->getConfig()['shipEndpoint']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($delivery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);
        \Monkey::app()->applicationReport(
            'UpsHandler',
            'addDelivery',
            'Called addDelivery to ' . $this->getConfig()['shipEndpoint'],
            json_encode($delivery));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport(
            'UPSHandler',
            'addDelivery',
            'Result addDelivery to ' . $this->getConfig()['shipEndpoint'],
            $result);
        try {
            $result = json_decode($result);
            try {
                $status = $result->ShipmentResponse->Response->ResponseStatus->Code;
            } catch (\Throwable $e) {
                throw new BambooException('Failed to addDelivery from UPS: ' . $result->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
            }
            if ($status == '1') {
                $shipment->trackingNumber = $result->ShipmentResponse->ShipmentResults->ShipmentIdentificationNumber;
                $shipment->update();
                return $shipment;
            }
        } catch (\Throwable $e) {
            //todo log me
            \Monkey::app()->applicationWarning('UpsHandler', 'addPickUp', 'Error while parsing response of addDelivery for ' . $shipment->printId(), $e);
        }
        return $shipment;
    }

    /**
     * @param CShipment $shipment
     * @return CShipment|bool
     */
    public function cancelPickUp(CShipment $shipment)
    {
        if(empty($shipment->bookingNumber)) return true;

        \Monkey::app()->applicationReport('CUPSHandler', 'cancelPickUp', 'Called cancelPickUp');
        $delivery = [
            'UPSSecurity' => $this->getUpsSecurity(),
            'PickupCancelRequest' => [
                'Request' => [
                    'TransactionReference' => [
                        'CustomerContext' => 'CustomerContext.' //???
                    ]
                ],
                'CancelBy' => '02', // what
                'PRN' => $shipment->bookingNumber,
            ]
        ];
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->getConfig('pickUpEndopoint'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($delivery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);
        \Monkey::app()->applicationReport(
            'UPSHandler',
            'cancelPickUp',
            'Request cancelPickUp to' . $this->getConfig('pickUpEndopoint'),
            json_encode($delivery));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport(
            'UPSHandler',
            'cancelPickUp',
            'Result cancelPickUp to ' . $this->getConfig()['pickUpEndopoint'],
            $result);
        try {
            $result = json_decode($result);
            try {
                $status = $result->PickupCancelResponse->Response->ResponseStatus->Code;
            } catch (\Throwable $e) {
                throw new BambooException('Failed to addPickup from UPS: ' . $result->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
            }

            if ($status == '1') {
                $shipment->bookingNumber = null;
                $shipment->update();

                return $shipment;
            }
        } catch (\Throwable $e) {
            \Monkey::app()->applicationWarning('UpsHandler', 'cancelPickUp', 'Error while parsing response of cancelPickUp for ' . $shipment->printId(), $e);
        }
        return false;
    }

    /**
     * @param CShipment $shipment
     * @return bool
     * @throws BambooException
     */
    public function cancelDelivery(CShipment $shipment)
    {
        if(empty($shipment->trackingNumber)) return true;

        $cancelRequest = [
            'UPSSecurity' => $this->getUpsSecurity(),
            'VoidShipmentRequest' => [
                'Request' => [
                    'TransactionReference' => [
                        'CustomerContext' => 'No Actual Context'
                    ]
                ],
                'VoidShipment' => [
                    'ShipmentIdentificationNumber' => $shipment->trackingNumber
                ]
            ]
        ];

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->getConfig('voidPackageEndpoint'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cancelRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);


        \Monkey::app()->applicationReport(
            'UpsHandler',
            'addDelivery',
            'Called cancelDelivery to ' . $this->getConfig()['voidPackageEndpoint'],
            json_encode($cancelRequest));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport(
            'UPSHandler',
            'addDelivery',
            'Result cancelDelivery to ' . $this->getConfig()['voidPackageEndpoint'],
            $result);

        if (!$result) {
            throw new BambooException($e);
        } else {
            $result = json_decode($result);
            try {
                if ($result->VoidShipmentResponse->Response->ResponseStatus->Code == '1') {
                    return true;
                } else throw new BambooException('Failed to Void Shipment');
            } catch (\Throwable $e) {
                throw new BambooException('Failed to Void Shipment from UPS: ' . $result->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
            }
        }
    }

    /**
     * @param $shippings
     * @return bool
     * @throws BambooException
     */
    public function closePendentShipping($shippings)
    {
        return true;
    }

    /**
     *
     */
    public function printDayShipping()
    {
        // TODO: Implement printDayShipping() method.
    }

    /**
     * @param $shipping
     */
    public function getBarcode($shipping)
    {
        // TODO: Implement getBarcode() method.
    }

    /**
     * @param CShipment $shipment
     * @return mixed
     * @throws BambooException
     */
    public function printParcelLabel(CShipment $shipment)
    {
        $labelRequest = [
            'UPSSecurity' => $this->getUpsSecurity(),
            'LabelRecoveryRequest' => [
                'LabelSpecification' => [
                    'LabelImageFormat' => [
                        'Code' => 'PDF'
                    ],
                    'HTTPUserAgent' => 'Mozilla/4.5'
                ],
                'TrackingNumber' => $shipment->trackingNumber
            ]
        ];

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->getConfig('labelRecoveryEndpoint'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($labelRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);

        \Monkey::app()->applicationReport(
            'UpsHandler',
            'addDelivery',
            'Called labelRecovery to ' . $this->getConfig()['labelRecoveryEndpoint'],
            json_encode($labelRequest));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);

        \Monkey::app()->applicationReport(
            'UpsHandler',
            'addDelivery',
            'Result labelRecovery to ' . $this->getConfig()['labelRecoveryEndpoint'],
            $result);

        if (!$result) {
            throw new BambooException($e);
        } else {
            $result = json_decode($result);
            try {
                if ($result->LabelRecoveryResponse->Response->ResponseStatus->Code == '1') {
                    return base64_decode($result->LabelRecoveryResponse->LabelResults->LabelImage->GraphicImage);
                } else throw new BambooException('Failed to recover Image');
            } catch (\Throwable $e) {
                throw new BambooException('Failed to recover Parcel Image from UPS: ' . $result->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
            }
        }
    }

    /**
     * @return array|string
     */
    public function listShippings()
    {

    }

    /**
     * @param CAddressBook $fromAddress
     * @param CAddressBook $toAddress
     * @param int $maxTry
     * @param \DateTime|null $dateTime
     * @return bool|\DateTime
     * @throws BambooException
     */
    public function getFirstPickUpDate(CAddressBook $fromAddress, CAddressBook $toAddress, $maxTry = 3, \DateTime $dateTime = null)
    {
        $dateTime = $dateTime ?? new \DateTime();
        $response = $this->requireTimeInTransit($fromAddress, $toAddress, $dateTime);
        $service = null;
        foreach ($response->TimeInTransitResponse->TransitResponse->ServiceSummary as $serviceSummary) {
            if ($serviceSummary->Service->Code == '25') {
                $service = $serviceSummary;
                break;
            }
        }
        if ($service) {
            if ($service->EstimatedArrival->Pickup->Date != $dateTime->format('Ymd')) {
                $newDateTime = $service->EstimatedArrival->Pickup->Date . ' 093000';
                return \DateTime::createFromFormat('Ymd His', $newDateTime);
            } elseif ($service->EstimatedArrival->CustomerCenterCutoff > $dateTime->format('His')) {
                return $dateTime;
            } elseif ($maxTry > 0) {
                return $this->getFirstPickUpDate($fromAddress, $toAddress, $maxTry - 1, SDateToolbox::GetNextWorkingDay($dateTime));
            } else throw  new BambooException('No Valid PickUp date Found for this address');
        } else throw new BambooException('Pickup Standard Service not available for this shipment');
    }

    /**
     * @param CAddressBook $fromAddress
     * @param CAddressBook $toAddress
     * @param \DateTime $pickUpDateTime
     * @return mixed
     * @throws BambooException
     */
    protected function requireTimeInTransit(CAddressBook $fromAddress, CAddressBook $toAddress, \DateTime $pickUpDateTime)
    {
        $labelRequest = [
            'UPSSecurity' => $this->getUpsSecurity(),
            'TimeInTransitRequest' => [
                'Request' => [
                    'RequestOption' => 'TNT',
                    'TransactionReference' => [
                        'CustomerContext' => 'No Actual Context'
                    ]
                ],
                'Pickup' => [
                    'Date' => $pickUpDateTime->format('Ymd'),
                    'Time' => $pickUpDateTime > (new \DateTime()) ? $pickUpDateTime->format('Hi') : (new \DateTime())->format('Hi'),
                ],
                'ShipFrom' => [
                    'Address' => [
                        'AddressLine' => $fromAddress->address . ' ' . $fromAddress->extra,
                        'City' => $fromAddress->city,
                        'PostalCode' => $fromAddress->postcode,
                        'CountryCode' => $fromAddress->country->ISO,
                        'ResidentialAddressIndicator' => 'N'
                    ]
                ],
                'ShipTo' => [
                    'Address' => [
                        'AddressLine' => $toAddress->address . ' ' . $toAddress->extra,
                        'City' => $toAddress->city,
                        'PostalCode' => $toAddress->postcode,
                        'CountryCode' => $toAddress->country->ISO
                    ]
                ],
                'ShipmentWeight' => [
                    'UnitOfMeasurement' => [
                        'Code' => 'KGS',
                        'Description' => 'Kilograms'
                    ],
                    'Weight' => '2.5'
                ]
            ]
        ];

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->getConfig('timeInTransitEndpoint'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($labelRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);

        \Monkey::app()->applicationReport(
            'UpsHandler',
            'addDelivery',
            'Called TimeInTransit to ' . $this->getConfig('timeInTransitEndpoint'),
            json_encode($labelRequest));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);

        \Monkey::app()->applicationReport(
            'UpsHandler',
            'addDelivery',
            'Result TimeInTransit to ' . $this->getConfig('timeInTransitEndpoint'),
            $result);

        if (!$result) {
            throw new BambooException($e);
        } else {
            $result = json_decode($result);
            try {
                if ($result->TimeInTransitResponse->Response->ResponseStatus->Code == '1') {
                    return $result;
                } else throw new BambooException('Failed to recover TimeInTransit');
            } catch (\Throwable $e) {
                throw new BambooException('Failed to recover TimeInTransit from UPS: ' . $result->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
            }
        }
    }
    public function getTracking(CShipment $shipment)
    {
        $orderLineHasShipmentRepo=\Monkey::app()->create('OrderLineHasShipment')->findOneBy(['shipmentId'=>$shipment->id]);
        $order=$orderLineHasShipmentRepo->orderId;

        $trackingRequest = [
            'UPSSecurity' => $this -> getUpsSecurity(),
            'TrackRequest' => [
                'Request' => [
                    'RequestOption' => '1',
                    'TransactionReference' => [
                        'CustomerContext' => 'spedizione numero:'.$shipment -> id.' ordine:'.$order]
                ],
                'InquiryNumber' => $shipment -> trackingNumber]

        ];

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this -> getConfig('trackRequest'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($trackingRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);

        \Monkey ::app() -> applicationReport(
            'UpsHandler',
            'GetTracking',
            'Called TimeInTransit to ' . $this -> getConfig('trackRequest'),
            json_encode($trackingRequest));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);

        \Monkey ::app() -> applicationReport(
            'UpsHandler',
            'GetTracking',
            'Result Tracking ' . $this -> getConfig('trackRequest'),
            $result);

        if (!$result) {
            throw new BambooException($e);
        } else {
            $result = json_decode($result);
            try {
                if ($result -> TrackRequest -> Response -> ResponseStatus -> Code == '1') {
                    return $result;
                } else throw new BambooException('Failed to recover TrackRequest');
            } catch (\Throwable $e) {
                throw new BambooException('Failed to recover TrackRequest from UPS: ' . $result -> Fault -> detail -> Errors -> ErrorDetail -> PrimaryErrorCode -> Description);
            }

        }
    }
}