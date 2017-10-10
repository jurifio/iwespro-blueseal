<?php

namespace bamboo\business\carrier;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CShipment;
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
class CUPSHandler extends ACarrierHandler
{

    protected $config = [
        'pickUpEndopoint' => 'https://onlinetools.ups.com/rest/Pickup',
        'shipEndpoint' => 'https://onlinetools.ups.com/rest/Ship',
        'voidPackageEndpoint' => 'https://onlinetools.ups.com/rest/Void',
        'labelRecoveryEndpoint' => 'https://onlinetools.ups.com/rest/LBRecovery',
        'ServiceAccessToken' => 'ED3442CCB18DBE8C',
        'UPSClientCode' => '463V1V',
        'username' => 'iwes123',
        'password' => 'Spedizioni123'
    ];

    protected $config2 = [
        'pickUpEndopoint' => 'https://onlinetools.ups.com/rest/Pickup',
        'shipEndpoint' => 'https://onlinetools.ups.com/rest/Ship',
        'voidPackageEndpoint' => 'https://onlinetools.ups.com/rest/Void',
        'labelRecoveryEndpoint' => 'https://onlinetools.ups.com/rest/LBRecovery',
        'ServiceAccessToken' => '4D32C405E147E40C',
        'UPSClientCode' => '463V1V',
        'username' => 'FabrizioMarconi',
        'password' => 'pKt)hT&n?^Q>gk*'
    ];

    protected $testConfig = [
        'pickUpEndopoint' => 'https://wwwcie.ups.com/rest/Pickup',
        'shipEndpoint' => 'https://wwwcie.ups.com/rest/Ship',
        'voidPackageEndpoint' => 'https://wwwcie.ups.com/rest/Void',
        'labelRecoveryEndpoint' => 'https://wwwcie.ups.com/rest/LBRecovery',
        'ServiceAccessToken' => '4D32C405E147E40C',
        'UPSClientCode' => '463V1V',
        'username' => 'FabrizioMarconi',
        'password' => 'pKt)hT&n?^Q>gk*'

    ];

    public function canPickUp()
    {
        return true;
    }

    /**
     * @param CShipment $shipment
     * @return CShipment|bool
     */
    public function addPickUp(CShipment $shipment)
    {
        \Monkey::app()->applicationReport('CUPSHandler', 'addPickup', 'Called addPickUp');
        $delivery = [
            'UPSSecurity' => $this->getUpsSecurity(),
            'PickupCreationRequest' => [
                'Request' => [
                    'TransactionReference' => [
                        'CustomerContext' => 'CustomerContext.' //???
                    ]
                ],
                'RatePickupIndicator' => 'Y',
                'TaxInformationIndicator' => 'Y',
                'PickupDateInfo' => [
                    'CloseTime' => '1900',
                    'ReadyTime' => '1700',
                    'PickupDate' => STimeToolbox::GetDateTime($shipment->predictedShipmentDate)->format('Ymd')
                ],
                'Shipper' => [
                    'Account' => [
                        'AccountNumber' => $this->getConfig()['UPSClientCode'],
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
            'GlsItalyHandler',
            'addPickup',
            'Request addPickup to' . $this->getConfig('pickUpEndopoint'),
            json_encode($delivery));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport(
            'GlsItalyHandler',
            'addDelivery',
            'Result addPickup to ' . $this->getConfig()['pickUpEndopoint'],
            $result);
        try {
            $result = json_decode($result);
            if ($result->PickupCreationResponse->Response->ResponseStatus->Code == '1') {
                $shipment->bookingNumber = $result->PickupCreationResponse->PRN;
                $shipment->update();

                return $this->addDelivery($shipment);
            }
        } catch (\Throwable $e) {
            //todo log me
            \Monkey::app()->applicationWarning('UpsHandler', 'addPickUp', 'Error while parsing response of addPickUp for ' . $shipment->printId(), $e);
        }
        return false;
    }

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
        if ($name) return $this->config[$name];
        return $this->config;
    }

    /**
     * @param CShipment $shipment
     * @return CShipment
     * @throws BambooException
     */
    public function addDelivery(CShipment $shipment)
    {
        \Monkey::app()->applicationReport('UpsHandler', 'addDelivery', 'Called addDelivery');
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
                        'Name' => $shipment->fromAddress->subject,
                        'ShipperNumber' => $this->getConfig()['UPSClientCode'],
                        'Address' => [
                            'AddressLine' => $shipment->fromAddress->address . ' ' . $shipment->fromAddress->extra,
                            'City' => $shipment->fromAddress->city,
                            'PostalCode' => $shipment->fromAddress->postcode,
                            'CountryCode' => $shipment->fromAddress->country->ISO
                        ]
                    ],
                    'ShipFrom' => [
                        'Name' => $shipment->fromAddress->subject,
                        'Address' => [
                            'AddressLine' => $shipment->fromAddress->address . ' ' . $shipment->fromAddress->extra,
                            'City' => $shipment->fromAddress->city,
                            'PostalCode' => $shipment->fromAddress->postcode,
                            'CountryCode' => $shipment->fromAddress->country->ISO
                        ]
                    ],
                    'ShipTo' => [
                        'Name' => $shipment->toAddress->subject,
                        'Address' => [
                            'AddressLine' => $shipment->toAddress->address . ' ' . $shipment->toAddress->extra,
                            'City' => $shipment->toAddress->city,
                            'PostalCode' => $shipment->toAddress->postcode,
                            'CountryCode' => $shipment->toAddress->country->ISO
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
                    'Service' => [
                        'Code' => '11',
                        'Description' => 'UPS Standard'
                    ],
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
            'GlsItalyHandler',
            'addDelivery',
            'Result addDelivery to ' . $this->getConfig()['shipEndpoint'],
            $result);
        try {
            $result = json_decode($result);
            if ($result->ShipmentResponse->Response->ResponseStatus->Code == '1') {
                $shipment->trackingNumber = $result->ShipmentResponse->ShipmentResults->ShipmentIdentificationNumber;
                $shipment->update();
                return $shipment;
            }
        } catch (\Throwable $e) {
            //todo log me
            \Monkey::app()->applicationWarning('UpsHandler', 'addPickUp', 'Error while parsing response of addPickUp for ' . $shipment->printId(), $e);
        }
        return $shipment;
    }

    /**
     * @param CShipment $shipment
     * @return bool
     * @throws BambooException
     */
    public function cancelDelivery(CShipment $shipment)
    {
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

        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        if (!$result) {
            throw new BambooException($e);
        } else {
            $result = json_decode($result);
            try {
                if($result->VoidShipmentResponse->Response->ResponseStatus->Code == '1') {
                    return true;
                } else throw new BambooException('Failed to Void Shipment');
            } catch (\Throwable $e) {
                throw new BambooException('Failed to Void Shipment from UPS: '.$result->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
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
                        'Code' => 'GIF'
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

        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        if (!$result) {
            throw new BambooException($e);
        } else {
            $result = json_decode($result);
            try {
                if($result->LabelRecoveryResponse->Response->ResponseStatus->Code == '1') {
                    return $result->LabelResults[0]->LabelImage->PDF417;
                } else throw new BambooException('Failed to recover Image');
            } catch (\Throwable $e) {
                throw new BambooException('Failed to recover Parcel Image from UPS: '.$result->Fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description);
            }
        }
    }

    /**
     * @return array|string
     */
    public function listShippings()
    {
        $url = $this->getConfig()['endpoint'] . '/ListSped';
        $data = [
            'SedeGls' => $this->getConfig()['SedeGls'],
            'CodiceClienteGls' => $this->getConfig()['CodiceClienteGls'],
            'PasswordClienteGls' => $this->getConfig()['PasswordClienteGls']
        ];

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        $postFields = http_build_query($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/x-www-form-urlencoded'
        ]);

        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        if (!$result) {
            var_dump($e);
            return "";
        } else {
            $dom = new \DOMDocument();
            $dom->loadXML($result);
            $parcels = [];
            foreach ($dom->getElementsByTagName('Parcel') as $rawParcel) {
                /** @var \DOMElement $rawParcel */
                $parcel = [];
                foreach ($rawParcel->childNodes as $key => $childNode) {
                    if (!isset($childNode->tagName) || !$childNode->tagName) continue;
                    $parcel[$childNode->tagName] = $childNode->nodeValue;
                }
                $parcels[] = $parcel;
            }
            return $parcels;
        }
    }

}