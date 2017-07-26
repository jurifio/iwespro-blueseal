<?php

namespace bamboo\business\carrier;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CShipment;

/**
 * Class CGlsItalyHandler
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
class CGlsItalyHandler extends ACarrierHandler
{

    protected $config = [
        'endpoint' => 'https://weblabeling.gls-italy.com/IlsWebService.asmx',
        'SedeGls' => 'MC',
        'CodiceClienteGls' => '136887',
        'PasswordClienteGls' => 'iwesnc',
        'CodiceContrattoGls' => '1108'
    ];

    /**
     * @param CShipment $shipment
     * @return CShipment
     * @throws BambooException
     */
    public function addDelivery(CShipment $shipment)
    {
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
        if (!$result) {
            var_dump($e);
        } else {
            $dom = new \DOMDocument();
            $dom->loadXML($result);
            $parcels = $dom->getElementsByTagName('Parcel');
            foreach ($parcels as $parcel) {
                /** @var \DOMElement $parcel */
                $ids = $parcel->getElementsByTagName('ContatoreProgressivo');
                /** @var \DOMNodeList $ids */
                if ($ids->item(0)->nodeValue == $shipment->id) {
                    $shipment->trackingNumber = $parcel->getElementsByTagName('NumeroSpedizione')->item(0)->nodeValue;
                    if ($shipment->trackingNumber == '999999999') throw new BambooException('Errore nella spedizione: ' . $parcel->getElementsByTagName('NoteSpedizione')->item(0)->nodeValue);
                    $shipment->update();
                    break;
                }
            }
        }

        return $shipment;
    }

    protected function writeParcel(\XMLWriter $xml, CShipment $shipment)
    {
        $xml->startElement('Parcel');
        $xml->writeElement('CodiceContrattoGls', $this->config['CodiceContrattoGls']);
        if (!empty($shipment->trackingNumber)) {
            $xml->writeElement('NumeroSpedizione', $shipment->trackingNumber);
        }

        $xml->writeElement('RegioneSociale', $shipment->toAddress->subject);
        $xml->writeElement('Indirizzo', $shipment->toAddress->address . ' ' . $shipment->toAddress->extra);
        $xml->writeElement('Localita', $shipment->toAddress->city);
        $xml->writeElement('Zipcode', $shipment->toAddress->postcode);
        $xml->writeElement('Provincia', $shipment->toAddress->province);
        //$xml->writeElement('Bda',$shipment->toAddress->subject);
        //$xml->writeElement('DataDocumentoTrasporto',$shipment->toAddress->subject);
        $xml->writeElement('Colli', 1);
        //$xml->writeElement('Incoterm',$shipment->toAddress->subject);
        $xml->writeElement('PesoReale', 2.5);

        if ($shipment->orderLine->getFirst()->order->orderPaymentMethod->name == 'contrassegno') {
            $xml->writeElement('ImportoContrassegno', $shipment->orderLine->getFirst()->order->netTotal);
        }

        $xml->writeElement('NoteSpedizione', $shipment->note);
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
     * @param CObjectCollection $shippings
     */
    public function closePendentShipping($shippings)
    {
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


        $url = $this->config['endpoint'] . '/CloseWorkingDay';
        $data = ['XMLInfoParcel' => $rawXml];

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
        if (!$result) {
            var_dump($e);
        } else {
            $dom = new \DOMDocument();
            $dom->loadXML($result);
            $parcels = $dom->getElementsByTagName('Parcel');
            foreach ($parcels as $parcel) {
                /** @var \DOMElement $parcel */
                $ids = $parcel->getElementsByTagName('ContatoreProgressivo');
                /** @var \DOMNodeList $ids */
                foreach ($shippings as $shipping) {
                    if ($ids->item(0)->nodeValue == $shipping->id) {
                        if ($shipping->trackingNumber == '999999999') throw new BambooException('Errore nella spedizione: ' . $parcel->getElementsByTagName('NoteSpedizione')->item(0)->nodeValue);
                        break;
                    }

                }
            }
        }
    }

    public function printDayShipping()
    {
        // TODO: Implement printDayShipping() method.
    }

    public function getBarcode($shipping)
    {
        // TODO: Implement getBarcode() method.
    }

    public function printParcelLabel(CShipment $shipment)
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement('GetPdf');
        $xml->writeAttribute('xmlns', 'https://weblabeling.gls-italy.com/');
        $xml->writeElement('SedeGls', $this->config['SedeGls']);
        $xml->writeElement('CodiceCliente', $this->config['CodiceClienteGls']);
        $xml->writeElement('Password', $this->config['PasswordClienteGls']);
        $xml->writeElement('CodiceContratto', $this->config['CodiceContrattoGls']);
        $xml->writeElement('ContatoreProgressivo', $shipment->id);
        $xml->endDocument();
        $rawXml = $xml->outputMemory();

        $url = $this->config['endpoint'] . '/GetPdf';
        $data = ['XMLInfoParcel' => $rawXml];

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
        if (!$result) {
            var_dump($e);
            return "";
        } else {
            return $result;
        }
    }
}