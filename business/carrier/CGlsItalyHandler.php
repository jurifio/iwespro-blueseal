<?php

namespace bamboo\business\carrier;
use bamboo\business\carrier\ACarrierHandler;
use bamboo\business\carrier\IImplementedPickUpHandler;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;
use \Throwable;
use \DateTime;
/**
 * Class CGlsItalyHandler
 * @package bamboo\business\carrier
 *
 * @author Iwes Team <juri@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CGlsItalyHandler extends ACarrierHandler implements IImplementedPickUpHandler
{

    protected $config = [
        'endpoint' => 'https://labelservice.gls-italy.com/ilswebservice.asmx',
        'SedeGls' => 'MC',
        'CodiceClienteGls' => '136887',
        'PasswordClienteGls' => 'Iwes2104',
        'CodiceContrattoGls' => '1108'
    ];
    /**
     * @param CShipment $shipment
     * @param $orderId
     * @return CShipment|bool
     * @throws \Throwable
     */
    public function addPickUp(CShipment $shipment,$orderId)
    {

        //funzione che genera la chiamata api
        \Monkey::app()->applicationReport('CGlsItalyHandler','addPickup','Called addPickUp');

        $shipmentReturn = $this->addDelivery($shipment,$orderId);
        return $shipmentReturn;

    }

    /**
     * @param CShipment $shipment
     * @param $orderId
     * @param $isOrderParallel
     * @param $orderToShipment
     * @return CShipment|bool
     * @throws BambooException
     */
    public function addDelivery(CShipment $shipment, $orderId)
    {
        \Monkey::app()->applicationReport('GlsItalyHandler','addDelivery','Called AddParcel');
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
        \Monkey::app()->applicationReport('GlsItalyHandler','addDelivery','Request AddParcel',$rawXml);
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,count($data));
        $postFields = http_build_query($data);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postFields);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            'Content-type: application/x-www-form-urlencoded'
        ]);

        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport('GlsItalyHandler','addDelivery','Result AddParcel',$result);
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

        }
        if($shipment->note!='') {
            $xml->writeElement('Notespedizione',$shipment->note);
        }

        $xml->writeElement('TipoPorto', 'F');
        $xml->writeElement('NoteAggiuntive', 'Order ' . $shipment->orderLine->getFirst()->order->id);
        $xml->writeElement('TipoCollo', '0');
        $xml->writeElement('Email', $shipment->orderLine->getFirst()->order->user->email);
        $xml->writeElement('Cellulare1', $shipment->toAddress->phone);
        if ($shipment->orderLine->getFirst()->order->orderPaymentMethod->name == 'contrassegno') {
            $xml->writeElement('ModalitaIncasso', 'CONT');
        }

        $now=(new \DateTime())->format('ymd');

        $xml->writeElement('GeneraPdf', 1);
        $xml->writeElement('ContatoreProgressivo', $shipment->id);
        $xml->writeElement('ValoreDichiarato', $shipment->orderLine->getFirst()->order->netTotal);
        $xml->writeElement('PersonaRiferimento', $shipment->toAddress->subject);
        $xml->writeElement('CategoriaMerceologica', '5');
        $xml->writeElement('FatturaDoganale', $shipment->orderLine->getFirst()->order->id);
        $xml->writeElement('DataFatturaDoganale', $now);
        $orderLine=\Monkey::app()->repoFactory->create('OrderLine')->findBy(['orderId' => $shipment->orderLine->getFirst()->order->id]);
        $numberLine=0;
        foreach($orderLine as $line){
            $numberLine++;
        }
        $xml->writeElement('DataFatturaDoganale', $now);
        $xml->writeElement('PezziDichiarati', $numberLine);
        $xml->writeElement('NazioneOrigine', 380);
        $xml->writeElement('TelefonoMittente', '0733471365');
        $xml->writeElement('NumeroFatturaCOD', $shipment->orderLine->getFirst()->order->id);
        $xml->writeElement('DataFatturaCOD', $now);
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
        \Monkey::app()->applicationReport(
            'GlsItaly',
            'addDelivery',
            'Called cancelDelivery to ' . $url,
            json_encode($data));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport(
            'GlsItaly',
            'addDelivery',
            'Result cancelDelivery to ' . $url,
            json_encode($data));
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
        \Monkey::app()->applicationReport('GlsItalyHandler','closePendentShipping','Called CloseWorkDay');
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
        \Monkey::app()->applicationReport('GlsItalyHandler','closePendentShipping','Request CloseWorkDay',$rawXml);
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey::app()->applicationReport('GlsItalyHandler','closePendentShipping','Response CloseWorkDay',$result);
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
     * @throws BambooException
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
            throw new BambooException($e);
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

    public function getFirstPickUpDate(CAddressBook $fromAddress,CAddressBook $toAddress)
    {
        $possibleDate = (new DateTime())->modify('+1 day');
        return $possibleDate;
    }

    public function addPickUpReturn(CShipment $shipment,$isShippingToIwes,$isOrderParallel,$orderToShipment)
    {
        // TODO: Implement addPickUpReturn() method.
    }

    public function cancelPickUp(CShipment $shipment)
    {
        // TODO: Implement cancelPickUp() method.
    }
}