<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

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
                    s.scope as scopo,
                    ol.shopId as shopId,
                    s.bookingNumber,
                    s.trackingNumber,
                    s.shipmentDate,
                    s.creationDate,
                    concat_ws(',',f.subject,f.city) as fromAddress,
                    concat_ws(',',t.subject,t.city) as toAddress
                FROM Shipment s 
                  join Carrier c on s.carrierId = c.id
                  left join AddressBook f on s.fromAddressId = f.id
                  left join AddressBook t on s.toAddressId = t.id
                  LEFT JOIN (
                     OrderLineHasShipment olhs
                     Join OrderLine ol on ol.orderId = olhs.orderId and ol.id = olhs.orderLineId
                    ) ON s.id = olhs.shipmentId
                  ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

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
            $row["DT_RowId"] = 'row__' . $val->printId();
            $row['id'] = $val->printId();
            $row['carrier'] = $val->carrier->name;
            $row['fromAddress'] = $val->toAddress ? ($val->toAddress->subject.'<br />'.$val->toAddress->city) : '---';
            $row['toAddress'] = $val->fromAddress ? ($val->fromAddress->subject.'<br />'.$val->fromAddress->city) : '---';
            $row['note'] = $val->note;
            $row['bookingNumber'] = $val->bookingNumber;
            $row['trackingNumber'] = $val->trackingNumber;
            $row['shipmentDate'] = $val->shipmentDate;
            $row['creationDate'] = $val->creationDate;
            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}