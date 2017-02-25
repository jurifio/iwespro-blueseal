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
                  left join ShopHasShippingAddressBook shabf on f.id = shabf.addressBookId
                  left join ShopHasShippingAddressBook shabt on t.id = shabt.addressBookId
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

        $res = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        $count = $this->app->repoFactory->create('Shipment')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Shipment')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($res as $raw) {

            $val = $this->app->repoFactory->create('Shipment')->findOne([$raw['id']]);
            $row = [];
            $row["DT_RowId"] = $val->printId();
            $row['id'] = $val->printId();

            $row['shop'] = $this->app->repoFactory->create('Shop')->findOne([$raw['shopId']])->name;
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