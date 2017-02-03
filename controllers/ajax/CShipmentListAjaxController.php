<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShipment;

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
                    shabf.shopId,
                    s.bookingNumber,
                    s.trackingNumber,
                    s.predictedShipmentDate,
                    s.creationDate,
                    concat_ws(',',f.subject,f.city) as fromAddress,
                    concat_ws(',',t.subject,t.city) as toAddress,
                    group_concat(concat_ws('-',ol.productId,ol.productVariantId)) as productContent,
                    group_concat(concat_ws('-',ol.id,ol.orderId)) as orderContent
                FROM Shipment s 
                  join Carrier c on s.carrierId = c.id
                  left join AddressBook f on s.fromAddressBookId = f.id
                  left join AddressBook t on s.toAddressBookId = t.id
                  left join ShopHasAddressBook shabf on f.id = shabf.addressBookId
                  left join ShopHasAddressBook shabt on t.id = shabt.addressBookId
                  LEFT JOIN (
                     OrderLineHasShipment olhs
                     Join OrderLine ol on ol.orderId = olhs.orderId and ol.id = olhs.orderLineId
                     ) ON s.id = olhs.shipmentId
                  GROUP BY s.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $allShop = $this->app->getUser()->hasPermission('allShops');
        if(!$allShop) {
            $datatable->addCondition('scope',[CShipment::SCOPE_SUPPLIER_TO_US]);
        }

        $datatable->addCondition('shopId',$this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $shipments = $this->app->repoFactory->create('Shipment')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->app->repoFactory->create('Shipment')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Shipment')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($shipments as $val) {
            $row = [];
            $row["DT_RowId"] = $val->printId();
            $row['id'] = $val->printId();

            $row['shop'] = 'todo';
            $row['carrier'] = $val->carrier->name;
            $row['bookingNumber'] = $val->bookingNumber;
            $row['trackingNumber'] = $val->trackingNumber;
            $row['toAddress'] = $val->toAddress ? ($val->toAddress->subject.'<br />'.$val->toAddress->city) : '---';
            $row['fromAddress'] = $val->fromAddress ? ($val->fromAddress->subject.'<br />'.$val->fromAddress->city) : '---';
            $row['predictedShipmentDate'] = $val->predictedShipmentDate ?
                (\DateTime::createFromFormat(DATE_MYSQL_FORMAT,$val->predictedShipmentDate))->format('Y-m-d') : "";
            $row['shipmentDate'] = $val->shipmentDate ?
                (\DateTime::createFromFormat(DATE_MYSQL_FORMAT,$val->shipmentDate))->format('Y-m-d') : "";
            $row['predictedDeliveryDate'] = $val->predictedDeliveryDate ?
                (\DateTime::createFromFormat(DATE_MYSQL_FORMAT,$val->predictedDeliveryDate))->format('Y-m-d') : "";
            $row['deliveryDate'] = $val->deliveryDate ?
                (\DateTime::createFromFormat(DATE_MYSQL_FORMAT,$val->deliveryDate))->format('Y-m-d') : $val->deliveryDate;
            $row['creationDate'] = $val->creationDate;
            $row['productContent'] = "";

            $orderlineIds = [];
            foreach ($val->orderLine as $orderLine) {
                if($allShop) $orderlineIds[] = '<a href="/blueseal/ordini/aggiungi?order='.$orderLine->orderId.'">'.$orderLine->printId().'</a>';
                else $orderlineIds[] = $orderLine->printId();
            }
            $row['orderContent'] = implode('<br />',$orderlineIds);
            $row['note'] = $val->note;

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}