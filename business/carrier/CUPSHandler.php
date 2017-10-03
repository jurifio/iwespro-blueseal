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
        'ServiceAccessToken' => '4D32C405E147E40C',
        'ServiceAccessToken2' => '9D339DDABFA49908',
        'UPSClientCode' => '463V1V'
    ];

    protected $testConfig = [
        'pickUpEndopoint' => 'https://wwwcie.ups.com/webservices/Pickup',
        'shipEndpoint' => 'https://wwwcie.ups.com/rest/Ship',
        'voidPackageEndpoint' => 'https://wwwcie.ups.com/rest/Void',
        'labelRecoveryEndpoint' => 'https://wwwcie.ups.com/rest/LBRecovery',
        'ServiceAccessToken' => '4D32C405E147E40C',
        'ServiceAccessToken2' => '9D339DDABFA49908',
        'UPSClientCode' => '463V1V'

    ];

    public function addPickUp(CShipment $shipment)
    {
        $delivery = [
            'UPSSecurity' => $this->getUpsSecurity($this->testConfig),
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
                        'AccountNumber' => '463V1V',
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
                        'Number' => '0733471365'//$shipment->fromAddress->phone ?? $shipment->fromAddress->cellphone
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
        var_dump($delivery);
        echo json_encode($delivery);
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->testConfig['testEndpoint']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($delivery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);

        $result = curl_exec($ch);
        $e = curl_error($ch);
        echo $result;
        var_dump($e);
        curl_close($ch);
    }

    protected function getUpsSecurity($config)
    {
        return [

            'UsernameToken' => [
                'Username' => 'FabrizioMarconi',
                'Password' => 'pKt)hT&n?^Q>gk*'
            ],
            'ServiceAccessToken' => [
                "AccessLicenseNumber" => $config['ServiceAccessToken']
            ]
        ];
    }

    /**
     * @param CShipment $shipment
     * @return CShipment
     * @throws BambooException
     */
    public function addDelivery(CShipment $shipment)
    {
        \Monkey::app()->applicationReport('UpsHandler', 'addDelivery', 'Called AddParcel');
        $delivery = [
            'UPSSecurity' => $this->getUpsSecurity($this->testConfig),
            'ShipmentRequest' => [
                'Request' => [
                    'RequestOption' => 'validate',
                    'TransactionReference' => [
                        'CustomerContext' => 'CustomerContext.' //???
                    ]
                ],
                'Shipment' => [
                    'Description' => 'Descrizione della spedizione', //
                    'Shipper' => [
                        'Name' => $shipment->fromAddress->subject,
                        'ShipperNumber' => '463V1V',
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
                                'AccountNumber' => '463V1V'
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
        var_dump($delivery);
        echo json_encode($delivery);
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->testConfig['shipEndpoint']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($delivery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept',
            'Access-Control-Allow-Methods: POST',
            'Access-Control-Allow-Origin: *',
            'Content-type: application/json'
        ]);

        $result = curl_exec($ch);
        $e = curl_error($ch);
        echo $result;
        var_dump($e);
        curl_close($ch);
    }

    /**
     * @param CShipment $shipment
     * @return bool|string
     */
    public function cancelDelivery(CShipment $shipment)
    {
        $url = $this->testConfig['endpoint'] . '/DeleteSped';

        $data = [
            'SedeGls' => $this->testConfig['SedeGls'],
            'CodiceClienteGls' => $this->testConfig['CodiceClienteGls'],
            'PasswordClienteGls' => $this->testConfig['PasswordClienteGls'],
            'NumSpedizione' => ltrim($shipment->trackingNumber, $this->testConfig['SedeGls'] . ' ')
        ];

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        $postFields = http_build_query($data);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/x-www-form-urlencoded'
        ]);

        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        if (!$result) {
            \Monkey::dump($e);
            return false;
        } else {
            $dom = new \DOMDocument();
            $dom->loadXML($result);
            return $dom->getElementsByTagName('DescrizioneErrore')->item(0)->nodeValue == "Eliminazione della spedizione " . $data['NumSpedizione'] . " avvenuta.";
        }
    }

    /**
     * @param $shippings
     * @return bool
     * @throws BambooException
     */
    public function closePendentShipping($shippings)
    {
        \Monkey::app()->applicationReport('GlsItalyHandler', 'closePendentShipping', 'Called CloseWorkDay');
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement('Info');
        $xml->writeElement('SedeGls', $this->testConfig['SedeGls']);
        $xml->writeElement('CodiceClienteGls', $this->testConfig['CodiceClienteGls']);
        $xml->writeElement('PasswordClienteGls', $this->testConfig['PasswordClienteGls']);

        foreach ($shippings as $shipping) {
            $this->writeParcel($xml, $shipping);
        }

        $xml->endDocument();
        $rawXml = $xml->outputMemory();


        $url = $this->testConfig['endpoint'] . '/CloseWorkDay';
        $data = ['XMLCloseInfoParcel' => $rawXml];

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        $postFields = http_build_query($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/x-www-form-urlencoded'
        ]);
        \Monkey::app()->applicationReport('GlsItalyHandler', 'closePendentShipping', 'Request CloseWorkDay', $rawXml);
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport('GlsItalyHandler', 'closePendentShipping', 'Response CloseWorkDay', $result);
        if (!$result) {
            throw new BambooException('Errore nella chiusura Giornata ' . $e);
        } else {
            $dom = new \DOMDocument();
            $dom->loadXML($result);
            $errore = $dom->getElementsByTagName('DescrizioneErrore')->item(0)->nodeValue;
            if ($errore == 'OK') {
                return true;
            } else {
                throw new BambooException('Errore nella chiusura Giornata ' . $errore);
            }
        }
    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool
     */
    protected function writeParcel(\XMLWriter $xml, CShipment $shipment)
    {
        $xml->startElement('Parcel');
        $xml->writeElement('CodiceContrattoGls', $this->testConfig['CodiceContrattoGls']);
        if (!empty($shipment->trackingNumber)) {
            $xml->writeElement('NumeroSpedizione', ltrim($shipment->trackingNumber, $this->testConfig['SedeGls'] . ' '));
        }

        $xml->writeElement('Ragionesociale', $shipment->toAddress->subject);
        $xml->writeElement('Indirizzo', $shipment->toAddress->address . ' ' . $shipment->toAddress->extra);
        $xml->writeElement('Localita', $shipment->toAddress->city);
        $xml->writeElement('Zipcode', $shipment->toAddress->postcode);
        $xml->writeElement('Provincia', $this->getProvinceCode($shipment->toAddress->province));
        $xml->writeElement('Bda', $shipment->orderLine->getFirst()->order->id);
        //$xml->writeElement('Bda',$shipment->toAddress->subject);
        //$xml->writeElement('DataDocumentoTrasporto',$shipment->toAddress->subject);
        $xml->writeElement('Colli', 1);
        //$xml->writeElement('Incoterm',$shipment->toAddress->subject);
        $xml->writeElement('PesoReale', 2.5);

        if ($shipment->orderLine->getFirst()->order->orderPaymentMethod->name == 'contrassegno') {
            $xml->writeElement('ImportoContrassegno', $shipment->orderLine->getFirst()->order->netTotal);
            $xml->writeElement('ModalitaIncasso', 'CONT');
        }

        $xml->writeElement('Notespedizione', $shipment->note);
        $xml->writeElement('NoteAggiuntive', 'Order ' . $shipment->orderLine->getFirst()->order->id);
        $xml->writeElement('TipoPorto', 'F');
        $xml->writeElement('TipoCollo', '0');

        $xml->writeElement('Email', $shipment->orderLine->getFirst()->order->user->email);
        $xml->writeElement('Cellulare1', $shipment->toAddress->phone);
        $xml->writeElement('GeneraPdf', 1);
        $xml->writeElement('ContatoreProgressivo', $shipment->id);
        $xml->endElement();
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
     * @return bool|string
     */
    public function printParcelLabel(CShipment $shipment)
    {
        $url = $this->testConfig['endpoint'] . '/GetPdf';
        $data = [
            'SedeGls' => $this->testConfig['SedeGls'],
            'CodiceCliente' => $this->testConfig['CodiceClienteGls'],
            'Password' => $this->testConfig['PasswordClienteGls'],
            'CodiceContratto' => $this->testConfig['CodiceContrattoGls'],
            'ContatoreProgressivo' => $shipment->id
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
            $binary = $dom->getElementsByTagName('base64Binary')->item(0)->nodeValue;
            return base64_decode($binary);
        }
    }

    /**
     * @return array|string
     */
    public function listShippings()
    {
        $url = $this->testConfig['endpoint'] . '/ListSped';
        $data = [
            'SedeGls' => $this->testConfig['SedeGls'],
            'CodiceClienteGls' => $this->testConfig['CodiceClienteGls'],
            'PasswordClienteGls' => $this->testConfig['PasswordClienteGls']
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