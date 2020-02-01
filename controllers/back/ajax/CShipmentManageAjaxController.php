<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CAddressBook;


/**
 * Class CShipmentManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/02/2020
 * @since 1.0
 */
class CShipmentManageAjaxController extends AAjaxController
{

    public function post()
    {
        $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
        $addressBookRepo = \Monkey::app()->repoFactory->create('AddressBook');
        $orderLineHasShipmentRepo = \Monkey::app()->repoFactory->create('OrderLineHasShipment');

        $data = $this->app->router->request()->getRequestData();
        if ($_GET['carrierId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Carrier Non selezionato</i>';
        } else {
            $carrierId = $_GET['carrierId'];
        }
        if ($_GET['deliveryDate'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">data consegna non inserita</i>';
        } else {
            $deliveryDate = strtotime($_GET['deliveryDate']);
            $deliveryDate = date('Y-m-d H:i:s',$deliveryDate);
        }

        if ($_GET['bookingNumber'] == '') {
            $bookingNumber = '';
        } else {
            $bookingNumber = $_GET['bookingNumber'];
        }
        if ($_GET['trackingNumber'] == '') {
            $trackingNumber = '';
        } else {
            $trackingNumber = $_GET['trackingNumber'];
        }
        if ($_GET['shopId'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Shop Non Selezionato</i>';
        } else {
            $shopId = $_GET['shopId'];
        }
        if ($_GET['scope'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;">Devi Selezionare il Tipo di spedizione </i>';
        } else {
            $scope = $_GET['scope'];
        }
        if ($_GET['realShipmentPrice'] == '') {
            return '<i style="color:red" class="fa fa-exclamation-triangle"></i><i style="color:red; font-family: \'Raleway\', sans-serif;line-height: 1.6;"> Prezzo Spedizione non inserito </i>';
        } else {
            $realShipmentPrice = $_GET['realShipmentPrice'];
        }
        if ($_GET['shipmentInvoiceNumber'] == '') {
            $shipmentInvoiceNumber = '';
        } else {
            $shipmentInvoiceNumber = $_GET['shipmentInvoiceNumber'];
        }
        if ($_GET['dateInvoice'] == '') {
            $dateInvoice = '';
        } else {
            $dateInvoice = strtotime($_GET['dateInvoice']);
            $dateInvoice = date('Y-m-d H:i:s',$dateInvoice);
        }
        if ($_GET['isOrder'] == '' || $_GET['isOrder'] == 0) {
            $isOrder = 0;
        } else {
            $isOrder = $_GET['isOrder'];
        }
        if ($_GET['order'] == '') {
            $order = '';
        } else {
            $order = $_GET['order'];
            $orderVal = explode('-',$order);
            $orderId = $orderVal[1];
            $orderLineId = $orderVal[0];
        }

        if ($_GET['fromAddressBookId'] == "") {
            $fromAddressBookId = '';
        } else {
            $fromAddressBookId = $_GET['fromAddressBookId'];
        }
        if ($_GET['fromName'] == "") {
            $fromName = '';
        } else {
            $fromName = $_GET['fromName'];
        }
        if ($_GET['fromSubject'] == "") {
            $fromSubject = $_GET['fromSubject'];
        } else {
            $fromSubject = $_GET['fromSubject'];
        }
        if ($_GET['fromAddress'] == "") {
            $fromAddress = '';
        } else {
            $fromAddress = $_GET['fromAddress'];
        }
        if ($_GET['fromExtra'] == '') {
            $fromExtra = '';
        } else {
            $fromExtra = $_GET['fromExtra'];
        }
        if ($_GET['fromCity'] == '') {
            $fromCity = '';
        } else {
            $fromCity = $_GET['fromCity'];
        }
        if ($_GET['fromCountryId'] == '') {
            $fromCountryId = '';
        } else {
            $fromCountryId = $_GET['fromCountryId'];
        }
        if ($_GET['fromPostCode'] == '') {
            $fromPostCode = '';
        } else {
            $fromPostCode = $_GET['fromPostCode'];
        }
        if ($_GET['fromPhone'] == '') {
            $fromPhone = '';
        } else {
            $fromPhone = $_GET['fromPhone'];
        }
        if ($_GET['fromCellphone'] == '') {
            $fromCellphone = '';
        } else {
            $fromCellphone = $_GET['fromCellphone'];
        }
        if ($_GET['fromVatNumber'] == '') {
            $fromVatNumber = '';
        } else {
            $fromVatNumber = $_GET['fromVatNumber'];
        }
        if ($_GET['fromProvince'] == '') {
            $fromProvince = '';
        } else {
            $fromProvince = $_GET['fromProvince'];
        }
        if ($_GET['fromIban'] == '') {
            $fromIban = '';
        } else {
            $fromIban = $_GET['fromIban'];
        }
        if ($_GET['toAddressBookId'] == "") {
            $toAddressBookId = '';
        } else {
            $toAddressBookId = $_GET['toAddressBookId'];
        }
        if ($_GET['toName'] == "") {
            $toName = '';
        } else {
            $toName = $_GET['toName'];
        }
        if ($_GET['toSubject'] == "") {
            $toSubject = $_GET['toSubject'];
        } else {
            $toSubject = $_GET['toSubject'];
        }
        if ($_GET['toAddress'] == "") {
            $toAddress = '';
        } else {
            $toAddress = $_GET['toAddress'];
        }
        if ($_GET['toExtra'] == '') {
            $toExtra = '';
        } else {
            $toExtra = $_GET['toExtra'];
        }
        if ($_GET['toCity'] == '') {
            $toCity = '';
        } else {
            $toCity = $_GET['toCity'];
        }
        if ($_GET['toCountryId'] == '') {
            $toCountryId = '';
        } else {
            $toCountryId = $_GET['toCountryId'];
        }
        if ($_GET['toPostCode'] == '') {
            $toPostCode = '';
        } else {
            $toPostCode = $_GET['toPostCode'];
        }
        if ($_GET['toPhone'] == '') {
            $toPhone = '';
        } else {
            $toPhone = $_GET['toPhone'];
        }
        if ($_GET['toCellphone'] == '') {
            $toCellphone = '';
        } else {
            $toCellphone = $_GET['toCellphone'];
        }
        if ($_GET['toVatNumber'] == '') {
            $toVatNumber = '';
        } else {
            $toVatNumber = $_GET['toVatNumber'];
        }
        if ($_GET['toProvince'] == '') {
            $toProvince = '';
        } else {
            $toProvince = $_GET['toProvince'];
        }
        if ($_GET['toIban'] == '') {
            $toIban = '';
        } else {
            $toIban = $_GET['toIban'];
        }


        /*****   fine fattura *///////

        try {
            $shipmentInsert = $shipmentRepo->getEmptyEntity();
            $shipmentInsert->carrierId = $carrierId;
            $shipmentInsert->scope = $scope;
            $shipmentInsert->bookingNumber = $bookingNumber;
            $shipmentInsert->trackingNumber = $trackingNumber;
            if ($deliveryDate != null) {
                $shipmentInsert->deliveryDate = $deliveryDate;
            }
            if ($fromAddressBookId != null) {
                $shipmentInsert->fromAddressBookId = $fromAddressBookId;
            } else {
                $addressBookInsert = $addressBookRepo->getEmptyEntity();
                $addressBookInsert->name = $fromName;
                $addressBookInsert->subject = $fromSubject;
                $addressBookInsert->address = $fromAddress;
                $addressBookInsert->extra = $fromExtra;
                $addressBookInsert->city = $fromCity;
                $addressBookInsert->countryId = $fromCountryId;
                $addressBookInsert->postcode = $fromPostCode;
                $addressBookInsert->phone = $fromCellphone;
                $addressBookInsert->vatNumber = $fromVatNumber;
                $addressBookInsert->province = $fromProvince;
                $addressBookInsert->iban = $fromIban;
                $addressBookInsert->insert();
                $findAddressBookUpdate = $addressBookRepo->findOneBy(['subject' => $fromSubject,'Address' => $fromAddress,'city' => $fromCity,'countryId' => $fromCountryId]);
                $fromAddressBookId = $findAddressBookUpdate->id;
                $findAddressBookUpdate->checksum = crc32($fromAddressBookId . $fromName . $fromSubject . $fromAddress . $fromExtra . $fromProvince . $fromCity . $fromPostCode . $fromCountryId . $fromPhone);
                $findAddressBookUpdate->update();
                $shipmentInsert->fromAddressBookId = $fromAddressBookId;
            }

            if ($toAddressBookId != null) {
                $shipmentInsert->toAddressBookId = $toAddressBookId;
            } else {
                $addressBookInsert = $addressBookRepo->getEmptyEntity();
                $addressBookInsert->name = $toName;
                $addressBookInsert->subject = $toSubject;
                $addressBookInsert->address = $toAddress;
                $addressBookInsert->extra = $toExtra;
                $addressBookInsert->city = $toCity;
                $addressBookInsert->countryId = $toCountryId;
                $addressBookInsert->postcode = $toPostCode;
                $addressBookInsert->phone = $toCellphone;
                $addressBookInsert->vatNumber = $toVatNumber;
                $addressBookInsert->province = $toProvince;
                $addressBookInsert->iban = $toIban;
                $addressBookInsert->insert();
                $findAddressBookUpdate = $addressBookRepo->findOneBy(['subject' => $fromSubject,'Address' => $fromAddress,'city' => $fromCity,'countryId' => $fromCountryId]);
                $toAddressBookId = $findAddressBookUpdate->id;
                $findAddressBookUpdate->checksum = crc32($toAddressBookId . $toName . $toSubject . $toAddress . $toExtra . $toProvince . $toCity . $toPostCode . $toCountryId . $toPhone);
                $findAddressBookUpdate->update();
                $shipmentInsert->toAddressBookId = $toAddressBookId;
            }

            if ($shipmentInvoiceNumber != null) {
                $shipmentInsert->shipmentInvoiceNumber = $shipmentInvoiceNumber;
            }
            if ($realShipmentPrice1 = null) {
                $shipmentInsert->realShipmentPrice = $realShipmentPrice;
            }
            if ($dateInvoice != null) {
                $shipmentInsert->dateInvoice = $dateInvoice;
            }
            $shipmentInsert->remoteShopShipmentId=$shopId;
            $shipmentInsert->insert();
            $res = \Monkey::app()->dbAdapter->query('select max(id) as id from Shipment ',[])->fetchAll();
            foreach ($res as $result) {
                $lastId = $result['id'];
            }
            if($isOrder!=null) {

                $insertOrderLineHasShipment = $orderLineHasShipmentRepo->getEmptyEntity();
                $insertOrderLineHasShipment->shipmentId = $lastId;
                $insertOrderLineHasShipment->orderLineId=$orderLineId;
                $insertOrderLineHasShipment-> ordeId=$orderId;
                $insertOrderLineHasShipment->insert();
                }


                \Monkey::app()->applicationLog('CShipmentAjaxController','Report','Insert Shipment','Insert id-shipment  ' . $lastId );
                return 'inserimento eseguito';

            }
        catch
            (\Throwable $e){
                \Monkey::app()->applicationLog('CRegistryClientManageAjaxController','error','insert Client',$e,'');
                return 'Errore Inserimento' . $e;
            }

    }



}