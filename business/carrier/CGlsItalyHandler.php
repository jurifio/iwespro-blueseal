<?php

namespace bamboo\business\carrier;

use bamboo\domain\entities\CShipment;

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

        $xml->startElement('Parcel');
        $xml->writeElement('CodiceContrattoGls', $this->config['CodiceContrattoGls']);
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
        $xml->endDocument();
        $rawXml = $xml->outputMemory();


        $url = $this->config['endpoint'] . '/AddParcel';
        $data = ['XMLInfoParcel' => $rawXml];

        /*
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) {
        }
        */
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
        if(!$result) {
            var_dump($e);
         } else {
            $dom = new \DOMDocument();
            $dom->loadXML($result);
            $parcels = $dom->getElementsByTagName('Parcel');
            foreach ($parcels as $parcel) {
                /** @var \DOMElement $parcel */
                $ids = $parcel->getElementsByTagName('ContatoreProgressivo');
                /** @var \DOMNodeList $ids */

                if($ids->item(0)->nodeValue == $shipment->id) {
                    $shipment->trackingNumber = $parcel->getElementsByTagName('NumeroSpedizione')->item(0)->nodeValue;
                    $shipment->update();
                    break;
                }
            }
        }


        return $shipment;
    }

    public function closePendentShipping($from = 0, $to = 'now')
    {
        // TODO: Implement closeAndPrintPendentShipping() method.
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
        // TODO: Implement printParcelLabel() method.
    }


}