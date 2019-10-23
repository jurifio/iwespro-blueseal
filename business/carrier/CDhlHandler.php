<?php

namespace bamboo\business\carrier;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CShipment;

/**
 * Class CDhlHandler
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
class CDhlHandler extends ACarrierHandler
{

    protected $config = [
        'endpoint' => 'http://xmlpitest-ea.dhl.com/XMLShippingServlet',
        'testSiteID' => 'DServiceVal',
        'CodiceClienteDHL' => '106971439',
        'testPasswordClienteDHL' => 'testServVal',
        'SiteID' => 'DServiceVal',
        'PasswordClienteDHL' => 'u7qVouSKHY',

    ];

    /**
     * @param CShipment $shipment
     * @return CShipment
     * @throws BambooException
     */
    public function addDelivery(CShipment $shipment)
    {
        \Monkey::app()->applicationReport('DHLHandler','addDelivery','Called AddParcel');
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement('req:ShipmentRequest');
        $xml->writeAttribute('xmlns:req','http://www.dhl.com');
        $xml->writeAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $xml->writeAttribute('xsi:schemaLocation','http://www.dhl.com ship-val-global-req.xsd');
        $xml->writeAttribute('schemaVersion','5.0');
        $xml->startElement('Request');
        $xml->startElement('ServiceHeader');
        $xml->writeElement('MessageTime',date(DATE_ATOM));
        $messaggeReference='Shipment_id_'.$shipment->id.'_order_'.$shipment->orderLine->getFirst()->order->id.'';
        $xml->writeElement('MessageReference',$messaggeReference);
        if (ENV=='dev') {
            $xml -> writeElement('SiteID', $this -> config['testSiteID']);
            $xml->writeElement('Password', $this->config['testPasswordClienteDHL']);
        }else {
            $xml->writeElement('SiteID',$this->config['SiteID']);
            $xml->writeElement('Password',$this->config['PasswordClienteDHL']);
        }
            $xml->endElement();
            $xml->endElement();
            $xml->writeElement('RegionCode', 'EU');
            $xml->writeElement('NewShipper', 'N');
            $xml->writeElement('LanguageCode', 'en');
            $xml->writeElement('PiecesEnabled', 'Y');
            $xml->startElement('Billing');
            $xml->writeElement('ShipperAccountNumber', $this->config['CodiceClienteDHL']);
            $xml->writeElement('ShippingPaymentType', 'S');
            $xml->writeElement('BillingAccountNumber', $this->config['CodiceClienteDHL']);
            $xml->writeElement('DutyPaymentType', 'R');
            $xml->endElement();


        $this->writeParcel($xml, $shipment);

        $xml->endDocument();
        $rawXml = $xml->outputMemory();


        $url = $this->config['endpoint'];
        $data = ['XMLInfoParcel' => $rawXml];
        \Monkey::app()->applicationReport('GlsItalyHandler','addDelivery','Request AddParcel',$rawXml);
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

            $toAddress[]=json_decode($shipment->orderLine->getFirst()->order->fronzenShippingAddress,true);

        $xml->startElement('Consignee');
        $xml->writeElement('CompanyName', $toAddress[0]['name'].' '.$toAddress[0]['surname'].' '.$toAddress[0]['company']);
        $xml->writeElement('AddressLine', $toAddress[0]['address'] . ' ' . $toAddress[0]['extra']);
        $xml->writeElement('City', $toAddress[0]['city']);
        $xml->writeElement('Division', $toAddress[0]['province']);
        $xml->writeElement('PostalCode', $toAddress[0]['postcode']);
        $countryRepo=\Monkey::app()->repoFactory->create('Country')->findOneBy(['id'=>$toAddress[0]['countryId']]);
        $countryISOCode=$countryRepo->ISO;
        $countryName=$countryRepo->name;
        $xml->writeElement('Division', $toAddress[0]['province']);
        $xml->writeElement('CountryCode',$countryISOCode);
        $xml->writeElement('CountryName',$countryName);
        $xml->startElement('Contact');

        $xml->writeElement('PersonName', $toAddress[0]['name']. ''. $toAddress[0]['surname']);
        $xml->writeElement('PhoneNumber', $toAddress[0]['phone']. ''. $toAddress[0]['surname']);
        $xml->endElement();
        $xml->endElement();
        $xml->startElement('ShipmentDetails');
        $xml->startElement('Pieces');
        if($countryRepo->extraue==1){
            $isDutiable="Y";
        }else{
            $isDutiable="N";
        }
       $orderId=$shipment->orderLine->getFirst()->order->id;
       $orderLine=\Monkey::app()->repoFactory->create('OrderLine')->findBy(['orderId'=>$orderId]);
      $numberOfPieces=0;
      $weight=0;
      $orderTotal=0;
       foreach ($orderLine as $lines){
           $xml->startElement('Piece');
           $xml->writeElement('PieceID',$lines->id);
           $xml->writeElement('PackageType','EE');
           $xml->writeElement('Weight','2.5');
           $xml->writeElement('DimWeight','1.0');
           $xml->writeElement('Width','20.5');
           $xml->writeElement('Depth','11.5');
           $xml->writeElement('Height','30');
           $xml->endElement();
           $numberOfPieces=$numberOfPieces+1;
           $weight=$weight+2.5;
           $orderTotal=$orderTotal+$lines->netPrice;
       }
       $xml->endElement();
       $xml->writeElement('IsDutiable',$isDutiable);
       $xml->writeElement('NumberOfPieces',$numberOfPieces);
       $xml->writeElement('Weight',$weight);
       $xml->writeElement('WeightUnit','K');

       if($countryRepo->id==110){
            $globalProductCode='N';
       }else{
           if($countryRepo->extraue==1){
               $globalProductCode='P';
           }else{
               $globalProductCode='W';
           }

       }
       $xml->writeElement('GlobalProductCode',$globalProductCode);
       $xml->writeElement('localProductCode',$globalProductCode);
       $xml->writeElement('Date',$shipment->predictedShipmentDate);
       $xml->writeElement('PackageType','EE');
       $xml->writeElement('IsDutiable','Y');
       $xml->writeElement('CurrencyCode','EUR');
       $xml->endElement();
       $xml->startElement('Shipper');
       $xml->writeElement('ShipperID',$this->config['CodiceClienteDHL']);
       $xml->writeElement('CompanyName','Iwes snc International Web Ecommerce Services ');
       $xml->writeElement('AddressLine','Via Cesare Pavese, 1');
       $xml->writeElement('City','Civitanova Marche');
       $xml->writeElement('Division','MC');
       $xml->writeElement('PostalCode','62012');
       $xml->writeElement('CountryCode','IT');
       $xml->writeElement('CountryName','Italy');
       $xml->startElement('Contact');
       $xml->writeElement('PersonName','delivery service');
       $xml->writeElement('PhoneNumber','+390733471365');
       $xml->writeElement('Email','gianluca@iwes.it');
       $xml->endElement();
       $xml->endElement();
        if ($shipment->orderLine->getFirst()->order->orderPaymentMethod->name == 'contrassegno') {
            $xml->startElement('SpecialService');
            $xml->writeElement('SpecialServiceType', 'KB');
            $xml->endElement();
        }
        $xml->startElement('Dutiable');
        $xml->writeElement('DeclaredValue', money_format('%.2n',$orderTotal));
        $xml->writeElement('DeclaredCurrency', 'EUR');
        $xml->endElement();
        $xml->startElement('Place');
        $xml->writeElement('ResidenceOrBusiness','B');
        $xml->writeElement('CompanyName','Iwes snc International Web Ecommerce Services ');
        $xml->writeElement('AddressLine','Via Cesare Pavese, 1');
        $xml->writeElement('City','Civitanova Marche');
        $xml->writeElement('Division','MC');
        $xml->writeElement('PostalCode','62012');
        $xml->writeElement('CountryCode','IT');
        $xml->writeElement('CountryName','Italy');
        $xml->writeElement('PackageLocation','iwes');
        $xml->endElement();
        $xml->writeElement('EProcShip', 'N');
        $xml->writeElement('LabelImageFormat', 'PDF');
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

}