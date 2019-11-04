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
class CDhlHandler extends  CDhlStopWatchHandler
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
        \Monkey ::app() -> applicationReport('DHLHandler', 'addDelivery', 'Called AddParcel');
        $xml = new \XMLWriter();
        $xml -> openMemory();
        $xml -> setIndent(true);
        $xml -> startDocument('1.0', 'utf-8');
        $xml -> startElement('req:ShipmentRequest');
        $xml -> writeAttribute('xmlns:req', 'http://www.dhl.com');
        $xml -> writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $xml -> writeAttribute('xsi:schemaLocation', 'http://www.dhl.com ship-val-global-req.xsd');
        $xml -> writeAttribute('schemaVersion', '5.0');
        $xml -> startElement('Request');
        $xml -> startElement('ServiceHeader');
        $xml -> writeElement('MessageTime', date(DATE_ATOM));
        $messaggeReference = 'Shipment_id_' . $shipment -> id . '_order_' . $shipment -> orderLine -> getFirst() -> order -> id . '';
        $xml -> writeElement('MessageReference', $messaggeReference);
        if (ENV == 'dev') {
            $xml -> writeElement('SiteID', $this -> config['testSiteID']);
            $xml -> writeElement('Password', $this -> config['testPasswordClienteDHL']);
        } else {
            $xml -> writeElement('SiteID', $this -> config['SiteID']);
            $xml -> writeElement('Password', $this -> config['PasswordClienteDHL']);
        }
        $xml -> endElement();
        $xml -> endElement();
        $xml -> writeElement('RegionCode', 'EU');
        $xml -> writeElement('NewShipper', 'N');
        $xml -> writeElement('LanguageCode', 'en');
        $xml -> writeElement('PiecesEnabled', 'Y');
        $xml -> startElement('Billing');
        $xml -> writeElement('ShipperAccountNumber', $this -> config['CodiceClienteDHL']);
        $xml -> writeElement('ShippingPaymentType', 'S');
        $xml -> writeElement('BillingAccountNumber', $this -> config['CodiceClienteDHL']);
        $xml -> writeElement('DutyPaymentType', 'R');
        $xml -> endElement();


        $this -> writeParcel($xml, $shipment);

        $xml -> endDocument();
        $rawXml = $xml -> outputMemory();


        $url = $this -> config['endpoint'];
        $data = ['XMLInfoParcel' => $rawXml];
        \Monkey ::app() -> applicationReport('GlsItalyHandler', 'addDelivery', 'Request AddParcel', $rawXml);
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
        \Monkey ::app() -> applicationReport('GlsItalyHandler', 'addDelivery', 'Result AddParcel', $result);
        if (!$result) {
            throw new BambooException($e);
        } else {
            $dom = new \DOMDocument();
            try {
                $dom -> loadXML($result);
            } catch (\Throwable $e) {
                throw new BambooException($result);
            }
            $parcels = $dom -> getElementsByTagName('Parcel');
            foreach ($parcels as $parcel) {
                /** @var \DOMElement $parcel */
                $ids = $parcel -> getElementsByTagName('ContatoreProgressivo');
                /** @var \DOMNodeList $ids */
                if ($ids -> item(0) -> nodeValue == $shipment -> id) {
                    $shipment -> trackingNumber = $this -> config['SedeGls'] . ' ' . $parcel -> getElementsByTagName('NumeroSpedizione') -> item(0) -> nodeValue;
                    if ($shipment -> trackingNumber == $this -> config['SedeGls'] . ' ' . '999999999') throw new BambooException('Errore nella spedizione: ' . $parcel -> getElementsByTagName('NoteSpedizione') -> item(0) -> nodeValue);
                    $shipment -> update();
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

        $toAddress[] = json_decode($shipment -> orderLine -> getFirst() -> order -> fronzenShippingAddress, true);

        $xml -> startElement('Consignee');
        $xml -> writeElement('CompanyName', $toAddress[0]['name'] . ' ' . $toAddress[0]['surname'] . ' ' . $toAddress[0]['company']);
        $xml -> writeElement('AddressLine', $toAddress[0]['address'] . ' ' . $toAddress[0]['extra']);
        $xml -> writeElement('City', $toAddress[0]['city']);
        $xml -> writeElement('Division', $toAddress[0]['province']);
        $xml -> writeElement('PostalCode', $toAddress[0]['postcode']);
        $countryRepo = \Monkey ::app() -> repoFactory -> create('Country') -> findOneBy(['id' => $toAddress[0]['countryId']]);
        $countryISOCode = $countryRepo -> ISO;
        $countryName = $countryRepo -> name;
        $xml -> writeElement('Division', $toAddress[0]['province']);
        $xml -> writeElement('CountryCode', $countryISOCode);
        $xml -> writeElement('CountryName', $countryName);
        $xml -> startElement('Contact');

        $xml -> writeElement('PersonName', $toAddress[0]['name'] . '' . $toAddress[0]['surname']);
        $xml -> writeElement('PhoneNumber', $toAddress[0]['phone'] . '' . $toAddress[0]['surname']);
        $xml -> endElement();
        $xml -> endElement();
        $xml -> startElement('ShipmentDetails');
        $xml -> startElement('Pieces');
        if ($countryRepo -> extraue == 1) {
            $isDutiable = "Y";
        } else {
            $isDutiable = "N";
        }
        $orderId = $shipment -> orderLine -> getFirst() -> order -> id;
        $orderLine = \Monkey ::app() -> repoFactory -> create('OrderLine') -> findBy(['orderId' => $orderId]);
        $numberOfPieces = 0;
        $weight = 0;
        $orderTotal = 0;
        foreach ($orderLine as $lines) {
            $xml -> startElement('Piece');
            $xml -> writeElement('PieceID', $lines -> id);
            $xml -> writeElement('PackageType', 'EE');
            $xml -> writeElement('Weight', '2.5');
            $xml -> writeElement('DimWeight', '1.0');
            $xml -> writeElement('Width', '20.5');
            $xml -> writeElement('Depth', '11.5');
            $xml -> writeElement('Height', '30');
            $xml -> endElement();
            $numberOfPieces = $numberOfPieces + 1;
            $weight = $weight + 2.5;
            $orderTotal = $orderTotal + $lines -> netPrice;
        }
        $xml -> endElement();
        $xml -> writeElement('IsDutiable', $isDutiable);
        $xml -> writeElement('NumberOfPieces', $numberOfPieces);
        $xml -> writeElement('Weight', $weight);
        $xml -> writeElement('WeightUnit', 'K');

        if ($countryRepo -> id == 110) {
            $globalProductCode = 'N';
        } else {
            if ($countryRepo -> extraue == 1) {
                $globalProductCode = 'P';
            } else {
                $globalProductCode = 'W';
            }

        }
        $xml -> writeElement('GlobalProductCode', $globalProductCode);
        $xml -> writeElement('localProductCode', $globalProductCode);
        $xml -> writeElement('Date', $shipment -> predictedShipmentDate);
        $xml -> writeElement('PackageType', 'EE');
        $xml -> writeElement('IsDutiable', 'Y');
        $xml -> writeElement('CurrencyCode', 'EUR');
        $xml -> endElement();
        $xml -> startElement('Shipper');
        $xml -> writeElement('ShipperID', $this -> config['CodiceClienteDHL']);
        $xml -> writeElement('CompanyName', 'Iwes snc International Web Ecommerce Services ');
        $xml -> writeElement('AddressLine', 'Via Cesare Pavese, 1');
        $xml -> writeElement('City', 'Civitanova Marche');
        $xml -> writeElement('Division', 'MC');
        $xml -> writeElement('PostalCode', '62012');
        $xml -> writeElement('CountryCode', 'IT');
        $xml -> writeElement('CountryName', 'Italy');
        $xml -> startElement('Contact');
        $xml -> writeElement('PersonName', 'delivery service');
        $xml -> writeElement('PhoneNumber', '+390733471365');
        $xml -> writeElement('Email', 'gianluca@iwes.it');
        $xml -> endElement();
        $xml -> endElement();
        if ($shipment -> orderLine -> getFirst() -> order -> orderPaymentMethod -> name == 'contrassegno') {
            $xml -> startElement('SpecialService');
            $xml -> writeElement('SpecialServiceType', 'KB');
            $xml -> endElement();
        }
        $xml -> startElement('Dutiable');
        $xml -> writeElement('DeclaredValue', money_format('%.2n', $orderTotal));
        $xml -> writeElement('DeclaredCurrency', 'EUR');
        $xml -> endElement();
        $xml -> startElement('Place');
        $xml -> writeElement('ResidenceOrBusiness', 'B');
        $xml -> writeElement('CompanyName', 'Iwes snc International Web Ecommerce Services ');
        $xml -> writeElement('AddressLine', 'Via Cesare Pavese, 1');
        $xml -> writeElement('City', 'Civitanova Marche');
        $xml -> writeElement('Division', 'MC');
        $xml -> writeElement('PostalCode', '62012');
        $xml -> writeElement('CountryCode', 'IT');
        $xml -> writeElement('CountryName', 'Italy');
        $xml -> writeElement('PackageLocation', 'iwes');
        $xml -> endElement();
        $xml -> writeElement('EProcShip', 'N');
        $xml -> writeElement('LabelImageFormat', 'PDF');
        $xml -> endElement();
        return true;
    }

    /**
     * @param CShipment $shipment
     * @return bool|string
     */
    public function cancelDelivery(CShipment $shipment)
    {
        $url = $this -> config['endpoint'] . '/DeleteSped';

        $data = [
            'SedeGls' => $this -> config['SedeGls'],
            'CodiceClienteGls' => $this -> config['CodiceClienteGls'],
            'PasswordClienteGls' => $this -> config['PasswordClienteGls'],
            'NumSpedizione' => ltrim($shipment -> trackingNumber, $this -> config['SedeGls'] . ' ')
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
        \Monkey ::app() -> applicationReport(
            'GlsItaly',
            'addDelivery',
            'Called cancelDelivery to ' . $url,
            json_encode($data));
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey ::app() -> applicationReport(
            'GlsItaly',
            'addDelivery',
            'Result cancelDelivery to ' . $url,
            json_encode($data));
        if (!$result) {
            \Monkey ::dump($e);
            return false;
        } else {
            $dom = new \DOMDocument();
            $dom -> loadXML($result);
            return $dom -> getElementsByTagName('DescrizioneErrore') -> item(0) -> nodeValue == "Eliminazione della spedizione " . $data['NumSpedizione'] . " avvenuta.";
        }
    }

    /**
     * @param $shippings
     * @return bool
     * @throws BambooException
     */
    public function closePendentShipping($shippings)
    {
        \Monkey ::app() -> applicationReport('GlsItalyHandler', 'closePendentShipping', 'Called CloseWorkDay');
        $xml = new \XMLWriter();
        $xml -> openMemory();
        $xml -> setIndent(true);
        $xml -> startDocument('1.0', 'utf-8');
        $xml -> startElement('Info');
        $xml -> writeElement('SedeGls', $this -> config['SedeGls']);
        $xml -> writeElement('CodiceClienteGls', $this -> config['CodiceClienteGls']);
        $xml -> writeElement('PasswordClienteGls', $this -> config['PasswordClienteGls']);

        foreach ($shippings as $shipping) {
            $this -> writeParcel($xml, $shipping);
        }

        $xml -> endDocument();
        $rawXml = $xml -> outputMemory();


        $url = $this -> config['endpoint'] . '/CloseWorkDay';
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
        \Monkey ::app() -> applicationReport('GlsItalyHandler', 'closePendentShipping', 'Request CloseWorkDay', $rawXml);
        $result = curl_exec($ch);
        $e = curl_error($ch);
        curl_close($ch);
        \Monkey ::app() -> applicationReport('GlsItalyHandler', 'closePendentShipping', 'Response CloseWorkDay', $result);
        if (!$result) {
            throw new BambooException('Errore nella chiusura Giornata ' . $e);
        } else {
            $dom = new \DOMDocument();
            $dom -> loadXML($result);
            $errore = $dom -> getElementsByTagName('DescrizioneErrore') -> item(0) -> nodeValue;
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
$utc_parsed_2 = str_replace(".45",".75",$utc_parsed_1);
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
     * @param \XMLWriter $xml
     * @param CShipment $shipment
     * @return bool|string
     */
    public function cancelPickup(\XMLWriter $xml, CShipment $shipment)
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


}