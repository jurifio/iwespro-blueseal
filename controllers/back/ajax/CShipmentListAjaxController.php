<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShipment;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CShipmentListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                    s.id,
                    c.name as carrier,
                    s.scope as scope,
                    shabf.shopId as shopId,
                    sh.name as shop,
                    s.bookingNumber,
                    s.trackingNumber,
                    s.predictedShipmentDate,
                    s.deliveryDate,
                    s.predictedDeliveryDate,
                    s.shipmentDate,
                    s.cancellationdate,
                    s.note,
                    s.creationDate,
                    concat_ws(',',f.subject,f.city) as fromAddress,
                    concat_ws(',',t.subject,t.city) as toAddress,
                    group_concat(concat_ws('-',ol.productId,ol.productVariantId)) as productContent,
                    group_concat(concat_ws('-',ol.id,ol.orderId)) as orderContent
                FROM Shipment s 
                  join Carrier c on s.carrierId = c.id
                  left join AddressBook f on s.fromAddressBookId = f.id
                  left join AddressBook t on s.toAddressBookId = t.id
                  left join ShopHasShippingAddressBook shabf on f.id = shabf.addressBookId
                  left join Shop sh on sh.id = shabf.shopId
                  left join ShopHasShippingAddressBook shabt on t.id = shabt.addressBookId
                  LEFT JOIN (
                     OrderLineHasShipment olhs
                     Join OrderLine ol on ol.orderId = olhs.orderId and ol.id = olhs.orderLineId
                     ) ON s.id = olhs.shipmentId
                  GROUP BY s.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $allShop = $this->app->getUser()->hasPermission('allShops');
      /*  if(!$allShop) {
            $datatable->addCondition('scope',[CShipment::SCOPE_SUPPLIER_TO_USER]);
        }*/

        $datatable->addCondition('shopId',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $val = \Monkey::app()->repoFactory->create('Shipment')->findOne([$row['id']]);

            $row["DT_RowId"] = $val->printId();
            $row['id'] = $val->printId();

            $row['shop'] = \Monkey::app()->repoFactory->create('Shop')->findOne([$row['shopId']])->name;
            $row['carrier'] = $val->carrier->name;
            $row['bookingNumber'] = $val->bookingNumber;
            $row['trackingNumber'] = $val->trackingNumber;
            $row['toAddress'] = $val->toAddress ? ($val->toAddress->subject.'<br />'.$val->toAddress->address.'<br />'.$val->toAddress->city) : '---';
            $row['fromAddress'] = $val->fromAddress ? ($val->fromAddress->subject.'<br />'.$val->fromAddress->address.'<br />'.$val->fromAddress->city) : '---';
            $row['predictedShipmentDate'] = STimeToolbox::FormatDateFromDBValue($val->predictedShipmentDate,'Y-m-d');
            $row['shipmentDate'] = STimeToolbox::FormatDateFromDBValue($val->shipmentDate,'Y-m-d');
            $row['predictedDeliveryDate'] = STimeToolbox::FormatDateFromDBValue($val->predictedDeliveryDate,'Y-m-d');
            $row['deliveryDate'] = STimeToolbox::FormatDateFromDBValue($val->deliveryDate,'Y-m-d');
            $row['cancellationDate'] = ($val->cancellationDate) ? '<span style="color-red">'
                . $val->cancellationDate . '<br />' . $val->shipmentFault->description . '</span>'
                : '';
            $row['creationDate'] = $val->creationDate;
            $row['productContent'] = "";


            $orderlineIds = [];
            $shippingSum = 0;
            foreach ($val->orderLine as $orderLine) {
                if($allShop) $orderlineIds[] = '<a href="/blueseal/ordini/aggiungi?order='.$orderLine->orderId.'">'.$orderLine->printId().'</a>';
                else $orderlineIds[] = $orderLine->printId();

                //SE LA SPEDIZIONE VA DAL FRIEND A IWES NON FARE LA SOMMMA E MOSTRA 0 SU COSTO DI SPEDIZIONE
                if($val->scope === "supplierToUs") {
                    $shippingSum = 0;
                } else {
                    $shippingSum += $orderLine->shippingCharge;
                }


            }

            $row['orderShipmentPrice'] = $shippingSum;
            $row['orderContent'] = implode('<br />',$orderlineIds);
            $row['note'] = $val->note;

            $row['shipmentInvoiceNumber'] = $val->shipmentInvoiceNumber;
            $row['realShipmentPrice'] = $val->realShipmentPrice;

            $margin = $shippingSum - $val->realShipmentPrice;

            if ($margin > 0) {
                $row['shipmentPriceMargin'] = "<p style='color:green'>".$margin."</p>";
            } else if ($margin < 0){
                $row['shipmentPriceMargin'] = "<p style='color:red'>".$margin."</p>";
            } else {
                $row['shipmentPriceMargin'] = $margin;
            }

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}