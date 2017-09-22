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
        'testEndpoint' => 'https://wwwcie.ups.com/rest/Pickup',
        'endpoint' => 'https://onlinetools.ups.com/rest/Pickup',
        'ServiceAccessToken' => '4D32C405E147E40C'
    ];

    public function addPickUp(CShipment $shipment)
    {
        $delivery = [
            'UPSSecurity' => [
                'UsernameToken' => [
                    'Username' => 'FabrizioMarconi',
                    'Password' => 'pKt)hT&n?^Q>gk*'
                ],
                'ServiceAccessToken' => [
                    "AccessLicenseNumber" => $this->config['ServiceAccessToken']
                ]
            ],
            'PickupCreationRequest' => [
                'Request' => [
                    'TransactionReference' => [
                        'CustomerContext' => 'CustomerContext. ??'
                    ]
                ]
            ],
            'RatePickupIndicator' => 'Y',
            'TaxInformationIndicator' => 'Y',
            'PickupDateInfo' => [
                'CloseTime' => '1900',
                'ReadyTime' => '1700',
                'PickupDate' => STimeToolbox::GetDateTime($shipment->predictedShipmentDate)->format('Ymd')
            ],
            'PickupAddress' => [
                'Company' => $shipment->fromAddress->subject,
                'AddressLine' => $shipment->fromAddress->address,
                'City' => $shipment->fromAddress->city,
                'StateProvince' => $this->getProvinceCode($shipment->fromAddress->province),
                'PostalCode' => $shipment->fromAddress->postcode,
                'CountryCode' => $shipment->fromAddress->country->ISO,
                'ResidentialIndicator' => 'N',
                'Phone' => [
                    'Number' => '07337735245'//$shipment->fromAddress->phone ?? $shipment->fromAddress->cellphone
                ]
            ],
            'AlternateAddressIndicator' => 'N',
            'PickupPiece' => [
                'ServiceCode' => 001,
                'Quantity' => 1,
                'DestinationCountryCode' => $shipment->toAddress->country->ISO,
                'ContainerCode' => 01
            ],
            'OverweightIndicator' => 'N',
            'PaymentMethod' => 00
        ];
        var_dump($delivery);
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->config['testEndpoint']);

        $postFields = http_build_query($delivery);
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
        var_dump($result);
        var_dump($e);
        curl_close($ch);
    }

    /**
     * @param CShipment $shipment
     * @return CShipment
     * @throws BambooException
     */
    public function addDelivery(CShipment $shipment)
    {

        \Monkey::app()->applicationReport('GlsItalyHandler', 'addDelivery', 'Called AddParcel');
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement('Info');
        $xml->writeElement('SedeGls', $this->config['SedeGls']);
        $xml->writeElement('CodiceClienteGls', $this->config['CodiceClienteGls']);
        $xml->writeElement('PasswordClienteGls', $this->config['PasswordClienteGls']);

        $this->writeParcel($xml, $shipment);

        $xml->endDocument();
        $rawXml = $xml->outputMemory();


        $url = $this->config['endpoint'] . '/AddParcel';
        $data = ['XMLInfoParcel' => $rawXml];
        \Monkey::app()->applicationReport('GlsItalyHandler', 'addDelivery', 'Request AddParcel', $rawXml);
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

        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport('GlsItalyHandler', 'addDelivery', 'Result AddParcel', $result);
        if (!$result) {
            throw new BambooException($e);
        } else {
            $dom = new \DOMDocument();
            try {
                $dom->loadXML($result);
            } catch (\Throwable $e) {
                throw new BambooException($result);
            }
            $parcels = $dom->getElementsByTagName('Parcel');
            foreach ($parcels as $parcel) {
                /** @var \DOMElement $parcel */
                $ids = $parcel->getElementsByTagName('ContatoreProgressivo');
                /** @var \DOMNodeList $ids */
                if ($ids->item(0)->nodeValue == $shipment->id) {
                    $shipment->trackingNumber = $this->config['SedeGls'] . ' ' . $parcel->getElementsByTagName('NumeroSpedizione')->item(0)->nodeValue;
                    if ($shipment->trackingNumber == $this->config['SedeGls'] . ' ' . '999999999') throw new BambooException('Errore nella spedizione: ' . $parcel->getElementsByTagName('NoteSpedizione')->item(0)->nodeValue);
                    $shipment->update();
                    break;
                }
            }
        }

        return $shipment;
    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool
     */
    protected function writeParcel(\XMLWriter $xml, CShipment $shipment)
    {
        $xml->startElement('Parcel');
        $xml->writeElement('CodiceContrattoGls', $this->config['CodiceContrattoGls']);
        if (!empty($shipment->trackingNumber)) {
            $xml->writeElement('NumeroSpedizione', ltrim($shipment->trackingNumber, $this->config['SedeGls'] . ' '));
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
     * @param CShipment $shipment
     * @return bool|string
     */
    public function cancelDelivery(CShipment $shipment)
    {
        $url = $this->config['endpoint'] . '/DeleteSped';

        $data = [
            'SedeGls' => $this->config['SedeGls'],
            'CodiceClienteGls' => $this->config['CodiceClienteGls'],
            'PasswordClienteGls' => $this->config['PasswordClienteGls'],
            'NumSpedizione' => ltrim($shipment->trackingNumber, $this->config['SedeGls'] . ' ')
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
        $xml->writeElement('SedeGls', $this->config['SedeGls']);
        $xml->writeElement('CodiceClienteGls', $this->config['CodiceClienteGls']);
        $xml->writeElement('PasswordClienteGls', $this->config['PasswordClienteGls']);

        foreach ($shippings as $shipping) {
            $this->writeParcel($xml, $shipping);
        }

        $xml->endDocument();
        $rawXml = $xml->outputMemory();


        $url = $this->config['endpoint'] . '/CloseWorkDay';
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
        $url = $this->config['endpoint'] . '/GetPdf';
        $data = [
            'SedeGls' => $this->config['SedeGls'],
            'CodiceCliente' => $this->config['CodiceClienteGls'],
            'Password' => $this->config['PasswordClienteGls'],
            'CodiceContratto' => $this->config['CodiceContrattoGls'],
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
        $url = $this->config['endpoint'] . '/ListSped';
        $data = [
            'SedeGls' => $this->config['SedeGls'],
            'CodiceClienteGls' => $this->config['CodiceClienteGls'],
            'PasswordClienteGls' => $this->config['PasswordClienteGls']
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