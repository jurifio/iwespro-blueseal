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
     * @return bool|string
     */
    public function bookPickup(\XMLWriter $xml, CShipment $shipment)
    {
        //Argument from runDHLClient.cmd
//Input for dhlclient path
        $arg0 = "";
//Input for directory path
        $arg1 = "";
//Input path for Request XML files
        $arg2 = "";
//Input path for Response XML Files
        $arg3 = "";
//Input path for Server url details
        $arg4 = "https://xmlpitest-ea.dhl.com/XMLShippingServlet";
//%FUTURE_DAY%
        $arg5 = false;
//%TIMEZONE%
        $arg6 = "+01:00";

//FILE PATH
        $dir_url = $arg1;
//REQUEST PATH & REQUEST FILE
        $filename = $arg1.$arg2;
//RESPONSE PATH
        $response_url = $arg1.$arg3;
//SERVER URL
        $server_url = $arg4;
//Future Date
        $futureDate = $arg5;

//Starting the StopWatch
        CDhlStopWatchHandler::start();

//IP ADDRESS
        $localIPAddress = getHostByName(getHostName());

//Set Cookie to store Client's IP address
        $_COOKIE['info[0]'] = $localIPAddress;

//Set Cookie to store filename that is being executed
        $_COOKIE['info[1]'] = $arg0;

//Setting timezone to UTC
        date_default_timezone_set("UTC");
        $utc = $arg6;
        $utc_parsed_1 = str_replace(":",".",$utc);
        $utc_parsed_2 = str_replace(".30",".50",$utc_parsed_1);
        $ts = (time() + ($utc_parsed_2*3600));
        $dtformat = "Y_m_d_H_i_s_";

//Set Cookie for timestamp after timezone is applied
        $_COOKIE['info[2]'] = $ts;

//Logger
        require_once('KLogger.php');
        $log = new KLogger ($dir_url."logs/DHLClient_".date('Ymd').".log" , KLogger::DEBUG );

        $count = 0;

        goto A;
        echo "\n";

        A:

//Getting the .xml file.
        $file = file_get_contents($filename, true);
        $len = strlen($file);

        $log->LogInfo(" | START DHLClient");
        $log->LogInfo(" | futureDate set to :: ".$futureDate);
        echo  "futureDate set to :: ".$futureDate."\n";

        $log->LogInfo(" | TimeZone set to :: UTC".$arg6);
        echo "TimeZone set to :: UTC".$arg6."\n";

//UTF-8 checking for .xml file.
        $encoding = mb_detect_encoding($file, 'UTF-8');
        if ($encoding == "UTF-8") {
            $new_server_url = $server_url.'?isUTF8Support=true';
            $reqxml= $file;
            $el_start = "<MessageReference>";
            $el_end = "</MessageReference>";
            $MessageReference = getBetween($reqxml,$el_start,$el_end);
            $log->LogInfo(" | isUTF8Support set to :: true");
        } else {
            $new_server_url = $server_url;
            $MessageReference = "";
            $log->LogWarn(" | isUTF8Support set to :: false");
        }

        $log->LogInfo($MessageReference." | Connecting to Server IP: ".$localIPAddress." URL:".$new_server_url);
        echo "Opening the connection ..... : ".$server_url."\n\n";
//echo "Connecting to Server IP: ".$localIPAddress." URL:".$new_server_url."\n\n";

//Check whether url exist.
        $invalidurl = "";
        $file_headers = @get_headers($new_server_url);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $invalidurl = true;
            $flushDNS = true;
            $retry = true;
        } else {
            $log->LogInfo($MessageReference." | Connected to IP: ".$localIPAddress." URL:".$new_server_url." | ".StopWatch::elapsed());
            $flushDNS = false;
            $retry = false;
        }

        if ($invalidurl == true) {
        }
        else {
            $log->LogInfo($MessageReference." | Begin sending request to XML Appl");

            if ($encoding == "UTF-8") {
                $post_header = 'Content-type: application/x-www-form-urlencoded'."\r\n".'Accept-Charset: UTF-8'."\r\n".'Content-Length: '.$len."\r\n".'futureDate: '.$futureDate."\r\n".'languageCode: PHP'."\r\n";
            }
            else {
                $post_header = 'Content-type: application/x-www-form-urlencoded'."\r\n".'Content-Length: '.$len."\r\n".'futureDate: '.$futureDate."\r\n".'languageCode: PHP'."\r\n";
            }

//Sending the Request
            $stream_options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $post_header,
                    'content' => $file
                )
            );

            $log->LogInfo($MessageReference." | Finish sending request to XML Appl | ".CDhlStopWatchHandler::elapsed());

            $log->LogInfo($MessageReference." | Begin receiving reply from XML Appl");

//Getting the response
            $context  = stream_context_create($stream_options);
            $response = file_get_contents($new_server_url, false, $context);
            $resxml=simplexml_load_string($response) or die("Error: Cannot create object");

            if ($response != "") {
//Get microtime and convert into miliseconds and assign to date for .xml file creation.
                $microstamp = microtime(true);
                $micro = sprintf("%06d",($microstamp - floor($microstamp)) * 1000);
                $milli = substr($micro, -3);
                $ndatetime = date($dtformat, $ts).$milli;

//Create and write response into .xml file.
                $action = fopen($response_url.$resxml->getName()."_".$ndatetime.'.xml', 'w') or die('Unable to open file!');
                fwrite($action, $response);
                fclose($action);

                $log->LogInfo($MessageReference." | Response received and saved successfully at :".$response_url);
                echo "Response received and saved successfully at :".$response_url."\n\n";
                $log->LogInfo($MessageReference." | The file name is:".$resxml->getName()."_".$ndatetime.".xml");
                echo "The file name is:".$resxml->getName()."_".$ndatetime.".xml \n\n";
            } else {
                $log->LogWarn($MessageReference."| Failed to receive response.");
                echo "Failed to receive response \n\n";
            }
            $log->LogInfo($MessageReference." | Finished receving reply from XML Appl | ".StopWatch::elapsed());

            $log->LogInfo($MessageReference." | Total time taken to process request and respond back to client | ".StopWatch::elapsed());
            echo "Total time taken to process request and respond back to client | ".StopWatch::elapsed()."\n";

            $log->LogInfo($MessageReference." | END DHLClient");

        }

//Unset Cookie
        unset($_COOKIE['ipaddress']);

//StopWatch


        function getBetween($reqxml,$el_start,$el_end){
            $el_config = explode($el_start, $reqxml);
            if (isset($el_config[1])){
                $el_config = explode($el_end, $el_config[1]);
                return $el_config[0];
            }
            return '';
        }

//Flush DNS
        if ($flushDNS == true) {
            $getOSName = PHP_OS_FAMILY;
            //Windows', 'BSD', 'Darwin', 'Solaris', 'Linux' or 'Unknown'.
            $count = $count + 1;

            if ($count > 1) {
            } else {
                echo "\n================= Please Wait for 60 seconds; Retry in progress ...... ================= \n\n";
                Switch ($getOSName) {
                    case "Windows": //Windows OS
                        $cmd_str = "ipconfig /flushdns";
                        $responsetxt = exec($cmd_str);
                        $log->LogInfo($MessageReference."WINDOWS OS -> ".$cmd_str." -> ".$responsetxt);
                        echo "WINDOWS OS -> ".$cmd_str." -> ".$responsetxt."\n\n";
                        break;

                    case "Darwin": //Macintosh
                        $cmd_str = "dscacheutil -flushcache";
                        $responsetxt = exec($cmd_str);
                        $log->LogInfo($MessageReference."MAC OS -> ".$cmd_str." -> ".$responsetxt);
                        echo "MAC OS -> ".$cmd_str." -> ".$responsetxt."\n\n";
                        break;

                    case "Linux": //Unix/Linux OS
                        $cmd_str_1 = "nscd -I hosts";
                        $responsetxt_1 = exec($cmd_str_1);
                        $log->LogInfo($MessageReference."Unix/Linux OS -> ".$cmd_str_1." -> ".$responsetxt_1);
                        echo "Unix/Linux OS -> ".$cmd_str_1." -> ".$responsetxt_1."\n\n";

                        $cmd_str_2 = "dnsmasq restart";
                        $responsetxt_2 = exec($cmd_str_2);
                        $log->LogInfo($MessageReference."Unix/Linux OS -> ".$cmd_str_2." -> ".$responsetxt_2);
                        echo "Unix/Linux OS -> ".$cmd_str_2." -> ".$responsetxt_2."\n\n";

                        $cmd_str_3 = "rndc restart";
                        $responsetxt_3 = exec($cmd_str_3);
                        $log->LogInfo($MessageReference."Unix/Linux OS -> ".$cmd_str_3." -> ".$responsetxt_3);
                        echo "Unix/Linux OS -> ".$cmd_str_3." -> ".$responsetxt_3."\n\n";
                        break;

                    case "Solaris":
                    case "BSD":
                    case "Unknown": //Unknown
                        $log->LogInfo($MessageReference." | Unable to flush DNS");
                        $log->LogWarn($MessageReference." | Unable to flush DNS");
                        echo "Unable to flush DNS \n\n";
                        break;
                }
                sleep(60);

            }
            if ($count > 3) {
                echo "=================    Three (3) retries are done - please contact DHL Support Team       ====================== \n\n";
                $log->LogInfo($MessageReference." | Total time taken to process request and respond back to client | ".StopWatch::elapsed());
                echo "Total time taken to process request and respond back to client | ".StopWatch::elapsed()."\n";
                $log->LogInfo($MessageReference." | END DHLClient");
                exit();
            } else {
                $log->LogInfo(" | RETRY =========> ".($count));
                echo "\nRETRY =========> ".($count)."\n";

                goto A;
            }
        } else {}
        return true;

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