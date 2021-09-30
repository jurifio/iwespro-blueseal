<?php

namespace bamboo\business\carrier;

use bamboo\business\carrier\ACarrierHandler;
use bamboo\business\carrier\IImplementedPickUpHandler;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\COrderLineHasShipment;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;
use DHL\Entity\EA\BookPickupRequest;
use DHL\Entity\EA\PickupResponse;
use DHL\Entity\GB\ShipmentResponse;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Datatype\AM\PieceType;
use DHL\Client\Web as WebserviceClient;
use DHL\Entity\EA\KnownTrackingRequest as Tracking;
use DHL\Datatype\GB\Piece;
use DHL\Datatype\GB\SpecialService;
use DHL\Entity\AM\GetQuote;


use DHL\Datatype\GB\Barcodes;

use DateTime;


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
class CDhlHandler extends ACarrierHandler implements IImplementedPickUpHandler
{

    /*  vecchia configurzione xml
    protected $config = [
         'endpoint' => 'http://xmlpitest-ea.dhl.com/XMLShippingServlet',
         'testSiteID' => 'DServiceVal',
         'CodiceClienteDHL' => '106971439',
         'testPasswordClienteDHL' => 'testServVal',
         'SiteID' => 'DServiceVal',
         'PasswordClienteDHL' => 'u7qVouSKHY',

     ];*/
    /**
     * @param CShipment $shipment
     * @param $orderId
     * @return CShipment|bool
     * @throws BambooException
     * @throws \Throwable
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    public function addPickUp(CShipment $shipment,$orderId )
    {

        //funzione che genera la chiamata api
        \Monkey::app()->applicationReport('CDhlHandler','addPickup','Called addPickUp');

        $shipmentReturn=$this->addDelivery( $shipment, $isShippingToIwes ,$isOrderParallel,$orderToShipment );
        return $shipmentReturn;

    }

    /**
     * @param CShipment $shipment
     * @param $orderId
     * @return CShipment
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    public function addDelivery(CShipment $shipment, $orderId)
    {
        \Monkey ::app() -> applicationReport('DHLHandler', 'addDelivery', 'Called AddParcel');
        /*recupero la riga  dell ordine e l ordine*/
        $orderLineHasShipment=\Monkey ::app() -> repoFactory -> create('OrderLineHasShipment')->findOneBy(['shipmentId'=>$shipment->id]);
        $shipmentFind=\Monkey ::app() ->repoFactory->create('Shipment')->findOneBy(['id'=>$shipment->id]);
        /*colleziono l'ordine*/
        $order=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id'=>$orderLineHasShipment->orderId]);
        //colleziono i dati del cliente
        $toAddress[] = json_decode($order->frozenShippingAddress, true);

        if(ENV=='dev'){
            require('/media/sf_sites/vendor/DHL-API-master/init.php');
        }else{
            require('/home/shared/vendor/DHL-API-master/init.php');
        }
        if (ENV == 'dev') {
            $SiteID = 'v62_FWcwlY5Chq';
            $Password = 'UO6VXNUV13';
        } else {
            $SiteID = 'v62_GcBntXbspo';
            $Password = 'u7qVouSKHY';
        }

        /*genero  il MessageTime*/
        $dateTime=(new DateTime())->format(DateTime::ATOM);
        $dhl = $config['dhl'];
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        /* genero il MessageReference */
        $randomString = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }



// creo la richiesta
        $sample =  new ShipmentRequest();

// assumo le variabili per il login
        $sample->SiteID = $SiteID;
        $sample->Password = $Password;

// setto i valori della richiesta
        $sample->MessageTime = $dateTime;
        $sample->MessageReference = $randomString;
        $sample->RegionCode = 'EU';
        $sample->RequestedPickupTime = 'Y';
        $sample->NewShipper = 'N';
        $countryRepo = \Monkey ::app() -> repoFactory -> create('Country') -> findOneBy(['id' => $toAddress[0]['countryId']]);
        if($toAddress[0]['countryId']!=110) {
            $sample->LanguageCode = 'en';
        }else{
            $sample->LanguageCode = 'it';
        }
        $sample->PiecesEnabled = 'Y';
        $sample->Billing->ShipperAccountNumber = $dhl['shipperAccountNumber'];
        $sample->Billing->ShippingPaymentType = 'S';
        $sample->Billing->BillingAccountNumber = $dhl['billingAccountNumber'];
        $sample->Billing->DutyPaymentType = 'S';
        $sample->Billing->DutyAccountNumber = $dhl['dutyAccountNumber'];
        $sample->Consignee->CompanyName = $toAddress[0]['name'] . ' ' . $toAddress[0]['surname'] . ' ' . $toAddress[0]['company'];
        $sample->Consignee->addAddressLine($toAddress[0]['address'].' '.$toAddress[0]['extra']);
        $sample->Consignee->City = $toAddress[0]['city'];
        $sample->Consignee->PostalCode = $toAddress[0]['postcode'];
        $countryISOCode = $countryRepo -> ISO;
        $countryName = $countryRepo -> name;
        $sample->Consignee->CountryCode = $countryISOCode;
        $sample->Consignee->CountryName =  $countryName;
        $sample->Consignee->Contact->PersonName = $toAddress[0]['name'] . '' . $toAddress[0]['surname'];
        $sample->Consignee->Contact->PhoneNumber = $toAddress[0]['phone'];
        $sample->Consignee->Contact->PhoneExtension = '';
        $sample->Consignee->Contact->FaxNumber = '';
        $sample->Consignee->Contact->Telex = '';
        $sample->Consignee->Contact->Email = '';
        $sample->Commodity->CommodityCode = 'cc';
        $sample->Commodity->CommodityName = 'cn';
        $orderId = $shipment -> orderLine -> getFirst() -> order -> id;
        $orderLine = \Monkey ::app() -> repoFactory -> create('OrderLine') -> findOneBy(['id'=>$orderLineHasShipment->orderLineId,'orderId' => $orderId]);
        $numberOfPieces = 1;
        $weight = 0;
        $orderTotal = 0;
        $sample->Dutiable->DeclaredValue = money_format('%.2n', $orderTotal);
        $sample->Dutiable->DeclaredCurrency = 'EUR';
        $sample->ShipmentDetails->NumberOfPieces = 1;

        $piece = new Piece();
        $piece->PieceID = '1';
        $piece->PackageType = 'EE';
        $piece->Weight = '1';
        $piece->DimWeight = '1';
        $piece->Width = '20';
        $piece->Height = '30';
        $piece->Depth = '15';
        $sample->ShipmentDetails->addPiece($piece);
        if ($countryRepo -> id == 110) {
            $globalProductCode = 'N';
        } else {
            if ($countryRepo -> extraue == 1) {
                $globalProductCode = 'P';
            } else {
                $globalProductCode = 'W';
            }

        }


        $sample->ShipmentDetails->Weight = '1';
        $sample->ShipmentDetails->WeightUnit = 'K';
        $sample->ShipmentDetails->GlobalProductCode = $globalProductCode;
        $sample->ShipmentDetails->LocalProductCode = $globalProductCode;
        $sample->ShipmentDetails->Date = date('Y-m-d');
        $sample->ShipmentDetails->Contents = 'AM international shipment contents';
        $sample->ShipmentDetails->DoorTo = 'DD';
        $sample->ShipmentDetails->DimensionUnit = 'I';
        $sample->ShipmentDetails->PackageType = 'EE';
        $sample->ShipmentDetails->IsDutiable = 'N';
        $sample->ShipmentDetails->CurrencyCode = 'EUR';
        $sample->Shipper->ShipperID = '106971439';
        $sample->Shipper->CompanyName = 'Iwes snc';
        $sample->Shipper->RegisteredAccount = '106971439';
        $sample->Shipper->addAddressLine('Via Cesare Pavese, 1');
        $sample->Shipper->City = 'Civitanova Marche';
        $sample->Shipper->Division = 'Macerata';
        $sample->Shipper->DivisionCode = 'mc';
        $sample->Shipper->PostalCode = '62012';
        $sample->Shipper->CountryCode = 'IT';
        $sample->Shipper->CountryName = 'Italy';
        $sample->Shipper->Contact->PersonName = 'delivery service';
        $sample->Shipper->Contact->PhoneNumber = '390733471365';
        $sample->Shipper->Contact->PhoneExtension = '';
        $sample->Shipper->Contact->FaxNumber = '390733471365';
        $sample->Shipper->Contact->Telex = '';
        $sample->Shipper->Contact->Email = 'ginaluca@iwes.it';

        if ($shipment -> orderLine -> getFirst() -> order -> orderPaymentMethod -> name == 'contrassegno') {
            $specialService = new SpecialService();
            $specialService->SpecialServiceType = 'KB';
            $sample->addSpecialService($specialService);
        }


        $sample->EProcShip = 'N';
        $sample->LabelImageFormat = 'PDF';

// chiamata a  DHL XML API
        $start = microtime(true);

// visualizza il risultato della chiamata

// Seleziona quale ambiente in base all  ENVIROMENT
        IF(ENV=='dev') {
            $client = new WebserviceClient('staging');
        }else{
            $client = new WebserviceClient('production');
        }

//CHIAMA IL SERVICE E VISUALIZZA IL RISULTATO
        $xml=$client->call($sample);
        $response = new ShipmentResponse();
        $response->initFromXML($xml);

// GENERA FILE PDF
        if(ENV=='dev') {
            file_put_contents('/media/sf_sites/cartechiniNew/client/public/themes/flatize/assets/shipment/' . $orderLineHasShipment->orderLineId . '-' . $orderLineHasShipment->orderId . '-dhl-label.pdf',base64_decode($response->LabelImage->OutputImage));
        }else{
            file_put_contents('/home/cartechini/public_html/client/public/themes/flatize/assets/shipment/' . $orderLineHasShipment->orderLineId . '-' . $orderLineHasShipment->orderId . '-dhl-label.pdf',base64_decode($response->LabelImage->OutputImage));
        }
        $shipmentFind->trackingNumber=$response->AirwayBillNumber;
        $bookingNumber = $this->bookPickup($shipment,$orderLineHasShipment,$trackingNumber,$globalProductCode,$numberOfPieces);
        $shipmentFind->bookingNumber=$bookingNumber;
        $shipmentFind->update();
// VISUALIZZA FILE PDF IN BROWSER/*
        /*   $data = base64_decode($response->LabelImage->OutputImage);
           if ($data)
           {
               header('Content-Type: application/pdf');
               header('Content-Length: ' . strlen($data));
           }*/
        return $shipmentFind;

    }



    public function getTracking($trackingNumber){
        if(ENV=='dev'){
            require('/media/sf_sites/vendor/DHL-API-master/init.php');
        }else{
            require('/home/shared/vendor/DHL-API-master/init.php');
        }
        $dateTime=(new DateTime())->format(DateTime::ATOM);
        $dhl = $config['dhl'];
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        if (ENV == 'dev') {
            $SiteID = 'v62_FWcwlY5Chq';
            $Password = 'UO6VXNUV13';
        } else {
            $SiteID = 'v62_GcBntXbspo';
            $Password = 'u7qVouSKHY';
        }

// INIZIALIZZO LA RICHIESTA
        $sample =  new ShipmentRequest();

// PASSO LE VARIABILI PER IL CLIENT
        $sample->SiteID = $SiteID;
        $sample->Password = $Password;
        // INIZIALIZZO LA RICHIESTA DI TRACKING
        $request = new Tracking();
        $request->SiteID = $dhl['id'];
        $request->Password = $dhl['pass'];
        $request->MessageReference = $randomString;
        $request->MessageTime = $dateTime;
        $request->LanguageCode = 'en';
        $request->AWBNumber = $trackingNumber;
        $request->LevelOfDetails = 'ALL_CHECK_POINTS';
        $request->PiecesEnabled = 'S';

        echo $request->toXML();
        $client = new WebserviceClient();
        $xml = $client->call($request);


        $result = new DHL\Entity\EA\TrackingResponse();
        $result->initFromXML($xml);
        return $result->toXML();


    }
    /**
    /**
     * @param CShipment $shipment
     * @param $orderId
     * @return CShipment
     * @throws \bamboo\core\exceptions\RedPandaException
     */

    public function getQuoteDelivery(CShipment $shipment, $orderId)
    {

        $orderLineHasShipment=\Monkey ::app() -> repoFactory -> create('OrderLineHasShipment')->findOneBy(['shipmentId'=>$shipment->id]);
        $order=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id'=>$orderLineHasShipment->orderId]);
        $toAddress[] = json_decode($order->frozenShippingAddress, true);
        $countryToAddress=\Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $toAddress[0]['countryId']]);
        $countryToAddressIso=$countryToAddress->ISO;

        $fromAddress=\Monkey::app()->repoFactory->create('AddressBook')->findOneBy(['id'=>$shipment->fromAddressBookId]);
        $country=\Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $fromAddress->countryId]);


        if(ENV=='dev'){
            require('/media/sf_sites/vendor/DHL-API-master/init.php');
        }else{
            require('/home/shared/vendor/DHL-API-master/init.php');
        }
        if (ENV == 'dev') {
            $SiteID = 'v62_FWcwlY5Chq';
            $Password = 'UO6VXNUV13';
        } else {
            $SiteID = 'v62_GcBntXbspo';
            $Password = 'u7qVouSKHY';
        }
        $dateTime=(new DateTime())->format(DateTime::ATOM);
        $dhl = $config['dhl'];
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $sample = new GetQuote();
        $sample->SiteID = $SiteID;
        $sample->Password = $Password;


// SETTO I VALORI DELLA REQUEST
        $sample->MessageTime = $dateTime;
        $sample->MessageReference = $randomString;
        $sample->BkgDetails->Date = date('Y-m-d');

        $piece = new PieceType();
        $piece->PieceID = 1;
        $piece->Height = 10;
        $piece->Depth = 30;
        $piece->Width = 15;
        $piece->Weight = 1;
        $sample->BkgDetails->addPiece($piece);
        $sample->BkgDetails->IsDutiable = 'Y';
        $sample->BkgDetails->QtdShp->QtdShpExChrg->SpecialServiceType = 'WY';
        $sample->BkgDetails->ReadyTime = 'PT10H21M';
        $sample->BkgDetails->ReadyTimeGMTOffset = '+01:00';
        $sample->BkgDetails->DimensionUnit = 'CM';
        $sample->BkgDetails->WeightUnit = 'KG';
        $sample->BkgDetails->PaymentCountryCode = 'CA';
        $sample->BkgDetails->IsDutiable = 'Y';

// RICHIEDO LA PAPERLESS
        $sample->BkgDetails->QtdShp->QtdShpExChrg->SpecialServiceType = 'WY';

        $sample->From->CountryCode = $country->ISO;
        $sample->From->Postalcode = $fromAddress->postCode;
        $sample->From->City = $fromAddress->city;

        $sample->To->CountryCode = $countryToAddressIso;
        $sample->To->Postalcode = $toAddress[0]['postcode'];
        $sample->To->City = $toAddress[0]['city'];
        $sample->Dutiable->DeclaredValue = $order->netTotal;
        $sample->Dutiable->DeclaredCurrency = 'EUR';

// ESEGUO CHIAMATA DHL
        $start = microtime(true);
        echo $sample->toXML();
        IF(ENV=='dev') {
            $client = new WebserviceClient('staging');
        }else{
            $client = new WebserviceClient('production');
        }
        $xml = $client->call($sample);
        echo PHP_EOL . 'Executed in ' . (microtime(true) - $start) . ' seconds.' . PHP_EOL;
        echo $xml . PHP_EOL;

    }

    /**
     * @param CShipment $shipment
     * @return bool|string
     */
    public function cancelDelivery(CShipment $shipment)
    {
        // TODO: Implement cancelDelivery() method.
    }

    /**
     * @param $shippings
     * @return bool
     * @throws BambooException
     */
    public function closePendentShipping($shippings)
    {
        // TODO: Implement closePendentShipping() method.
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
        $url = $this -> config['endpoint'] . '/GetPdf';
        $data = [
            'SedeGls' => $this -> config['SedeGls'],
            'CodiceCliente' => $this -> config['CodiceClienteGls'],
            'Password' => $this -> config['PasswordClienteGls'],
            'CodiceContratto' => $this -> config['CodiceContrattoGls'],
            'ContatoreProgressivo' => $shipment -> id
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
            $dom -> loadXML($result);
            $binary = $dom -> getElementsByTagName('base64Binary') -> item(0) -> nodeValue;
            return base64_decode($binary);
        }
    }

    /**
     * @return array|string
     */
    public function listShippings()
    {
        $url = $this -> config['endpoint'] . '/ListSped';
        $data = [
            'SedeGls' => $this -> config['SedeGls'],
            'CodiceClienteGls' => $this -> config['CodiceClienteGls'],
            'PasswordClienteGls' => $this -> config['PasswordClienteGls']
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
            //  var_dump($e);
            return "";
        } else {
            $dom = new \DOMDocument();
            $dom -> loadXML($result);
            $parcels = [];
            foreach ($dom -> getElementsByTagName('Parcel') as $rawParcel) {
                /** @var \DOMElement $rawParcel */
                $parcel = [];
                foreach ($rawParcel -> childNodes as $key => $childNode) {
                    if (!isset($childNode -> tagName) || !$childNode -> tagName) continue;
                    $parcel[$childNode -> tagName] = $childNode -> nodeValue;
                }
                $parcels[] = $parcel;
            }
            return $parcels;
        }
    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @param COrderLineHasShipment $orderLineHasShipment
     * @param $trackingNumber int
     * @param $globalProductCode string
     * @param $numberOfPieces string
     * @return bool|string
     */
    public function bookPickup(CShipment $shipment,COrderLineHasShipment $orderLineHasShipment,$trackingNumber,$globalProductCode,$numberOfPieces)
    {

        if (ENV == 'dev') {
            $SiteID = 'v62_FWcwlY5Chq';
            $Password = 'UO6VXNUV13';
        } else {
            $SiteID = 'v62_GcBntXbspo';
            $Password = 'u7qVouSKHY';
        }


        $client = new WebserviceClient('production');
        $orderLineGet=\Monkey::app()->repoFactory->create('OrderLine')->findOneBy(['orderId' => $orderLineHasShipment->orderId,'id'=>$orderLineHasShipment->orderLineId]);
        $sku=\Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId' => $orderLineGet->productId,'productVariantId'=>$orderLineGet->productVariantId,'shopId'=>$orderLineGet->shopId,'productSizeId'=>$orderLineGet->productSizeId]);
        $dirtyProduct=\Monkey::app()->repoFactory->create('DirtyProduct')->findOneBy(['productId'=>$sku->productId,'productVariantId'=>$sku->productVariantId,'shopId'=>$sku->shopId]);
        $dirtySkus=\Monkey::app()->repoFactory->create('DirtySku')->findBy(['dirtyProductId'=>$dirtyProduct->id,'shopId'=>$orderLineGet->shopId,'productSizeId'=>$orderLineGet->productSizeId]);
        $order=\Monkey::app()->repoFactory->create('Order')->findOneBy(['id' => $orderLineHasShipment->orderId]);
        $address = \bamboo\domain\entities\CUserAddress::defrost($order->frozenShippingAddress);
        $storeHouseId=1;
        foreach($dirtySkus as $dirtySku) {
            if ($dirtySku->qty > 0) {
                $storeHouseId = $dirtySku->storeHouseId;
                break;
            }
        }
        $shop=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$orderLineGet->shopId]);
        $storehouse=\Monkey::app()->repoFactory->create('StoreHouse')->findOneBy(['id'=>$storeHouseId,'shopId'=>$orderLineGet->shopId]);





        $pickup = new BookPickupRequest();
        $pickup->SiteID = $SiteID;
        $pickup->Password = $Password;
        $pickup->MessageTime = (new DateTime())->format(DateTime::ATOM);
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        /* genero il MessageReference */
        $randomString = '';
        for ($i = 0; $i < 32; $i++) {
            $randomString .= $characters[rand(0,$charactersLength - 1)];
        }
        $country=\Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $storehouse->countryId]);
        $pickup->MessageReference = $randomString;
        $datePickUp=(new DateTime($order->orderDate))->modify('+1 day');
        $countryCustomer=\Monkey::app()->repoFactory->create('Country')->findOneBy(['id'=>$address->countryId]);
        $xmlToSend= '<?xml version="1.0" encoding="UTF-8"?>
<req:BookPURequest xmlns:req="http://www.dhl.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com book-pickup-global-req_EA.xsd" schemaVersion="3.0">
	<Request>
		<ServiceHeader>
			<MessageTime>'.(new DateTime())->format(DateTime::ATOM).'</MessageTime>
			<MessageReference>'.$randomString.'</MessageReference>
            <SiteID>'.$SiteID.'</SiteID>
			<Password>'.$Password.'</Password>
		</ServiceHeader>
		<MetaData>
			<SoftwareName>XMLPI</SoftwareName>
			<SoftwareVersion>3.0</SoftwareVersion>
		</MetaData>
	</Request>
	<Requestor>
		<AccountType>D</AccountType>
		<AccountNumber>106971439</AccountNumber>
		<RequestorContact>
			<PersonName>'.$shop->name.'</PersonName>
			<Phone>'.$storehouse->phone.'</Phone>
		</RequestorContact>
		<CompanyName>'.$shop->title.'</CompanyName>
		<Address1>'.$storehouse->address.'</Address1>
		<Address2>'.$storehouse->number.'</Address2>
		<City>'.$storehouse->city.'</City>
		<CountryCode>GB</CountryCode>
		<PostalCode>'. $storehouse->cap.'</PostalCode>
	</Requestor>
	<Place>
		<LocationType>B</LocationType>
		<CompanyName>'.$shop->name.'</CompanyName>
		<Address1>'.$storehouse->address.'</Address1>
		<Address2>'.$storehouse->number.'</Address2>
		<PackageLocation>Reception</PackageLocation>
		<City>'.$storehouse->city.'</City>
		<CountryCode>'.$country->ISO.'</CountryCode>
		<PostalCode>'. $storehouse->cap.'</PostalCode>
	</Place>
	<Pickup>
		<PickupDate>'.$datePickUp->format('Y-m-d').'</PickupDate>
		<PickupTypeCode>A</PickupTypeCode>
		<ReadyByTime>16:00</ReadyByTime>
		<CloseTime>19:00</CloseTime>
		<Pieces>1</Pieces>
		<RemotePickupFlag>Y</RemotePickupFlag>
		<weight>
			<Weight>1</Weight>
			<WeightUnit>K</WeightUnit>
		</weight>
	</Pickup>
	<PickupContact>
		<PersonName>'.$storehouse->name.'</PersonName>
		<Phone>'. $storehouse->phone.'</Phone>
	</PickupContact>
	<ShipmentDetails>
		<AccountType>D</AccountType>
		<AccountNumber>106971439</AccountNumber>
		<BillToAccountNumber>106971439</BillToAccountNumber>
		<AWBNumber>'.$trackingNumber.'</AWBNumber>
		<NumberOfPieces>'.$numberOfPieces.'</NumberOfPieces>
		<Weight>1</Weight>
		<WeightUnit>K</WeightUnit>
		<GlobalProductCode>'.$globalProductCode.'</GlobalProductCode>
		<LocalProductCode>'.$globalProductCode.'</LocalProductCode>
		<DoorTo>DD</DoorTo>
		<DimensionUnit>C</DimensionUnit>
		<Pieces>
			<Piece>
				<Weight>1</Weight>
				<Width>15</Width>
				<Height>25</Height>
				<Depth>15</Depth>
			</Piece>
		</Pieces>
	</ShipmentDetails>
	<ConsigneeDetails>
		<CompanyName>'.$address->company.' '.$address->name.' '.$address->surname.'</CompanyName>
		<AddressLine>'.$address->address.'</AddressLine>
		<City>'.$address->city.'</City>
		<CountryCode>'.$countryCustomer->ISO.'</CountryCode>
		<PostalCode>'.$address->postcode.'</PostalCode>
		<Contact>
			<PersonName>'.$address->name.' '.$address->surname.'</PersonName>
			<Phone>'.$address->phone.'</Phone>
		</Contact>
	</ConsigneeDetails>
</req:BookPURequest>';
//$pickup->toXml();
//$prova=$pickup->toXml();
//$xml = $client->call($xmlToSend);
        if (!$ch = curl_init())
        {
            throw new \Exception('could not initialize curl');
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, 'https://xmlpi-ea.dhl.com/XMLShippingServlet?isUTF8Support=true');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_PORT , 443);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlToSend);
        $result = curl_exec($ch);

        if (curl_error($ch))
        {
            return false;
        }
        else
        {
            curl_close($ch);
        }

        $xml=$result;
        $resultCall = new PickupResponse();
        $resultCall->initFromXML($xml);
        $bookingNumber=$resultCall->ConfirmationNumber;
        return $bookingNumber;

    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool|string
     */
    public function modifyPickup(\XMLWriter $xml, CShipment $shipment)
    {

        return true;
    }

    /**
     * @param CShipment $shipment
     * @return bool|string
     */
    public function cancelPickup( CShipment $shipment)
    {

        return true;
    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool|string
     */
    public function getCapability(\XMLWriter $xml, CShipment $shipment)
    {

        return true;
    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool|string
     */
    public function getQuote(\XMLWriter $xml, CShipment $shipment)
    {

        return true;
    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool|string
     */
    public function shipmentValidation(\XMLWriter $xml, CShipment $shipment)
    {
        return true;
    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool|string
     */
    public function trackingKnow(\XMLWriter $xml, CShipment $shipment)
    {
        return true;
    }

    /**
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool|string
     */
    public function trackingUnKnow(\XMLWriter $xml, CShipment $shipment)
    {
        return true;
    }


    public function getFirstPickUpDate(CAddressBook $fromAddress,CAddressBook $toAddress)
    {
        $possibleDate=(new DateTime())->modify('+3 day');
        return $possibleDate;
    }



    public function addPickUpReturn(CShipment $shipment,$isShippingToIwes,$isOrderParallel,$orderToShipment)
    {
        // TODO: Implement addPickUpReturn() method.
    }



}