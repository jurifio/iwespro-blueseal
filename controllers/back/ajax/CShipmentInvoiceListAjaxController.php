<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShipment;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CShipmentInvoiceListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/02/2020
 * @since 1.0
 */
class CShipmentInvoiceListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                    s.id,
                    c.`name` as carrier,
                    s.scope as scope,
                    s.bookingNumber,
                    s.shipmentInvoiceNumber,    
       				s.realShipmentPrice,
                    s.trackingNumber,
                    s.predictedShipmentDate,
                    s.deliveryDate,
                    s.predictedDeliveryDate,
                    s.shipmentDate,
                    s.cancellationdate,
                    s.note,
                    s.remoteShipmentId as remoteShipmentId,
                     s.dateInvoice as dateInvoice,
                    s.remoteShopShipmentId as shopId
                
                  
                FROM Shipment s 
                  join Carrier c on s.carrierId = c.id
                where s.shipmentInvoiceNumber is not null
                  GROUP BY s.shipmentInvoiceNumber";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $allShop = $this->app->getUser()->hasPermission('allShops');
        if(!$allShop) {
            $datatable->addCondition('scope',[CShipment::SCOPE_SUPPLIER_TO_USER,CShipment::SCOPE_SUPPLIER_TO_US,CShipment::SCOPE_US_TO_USER,CShipment::SCOPE_US_TO_SUPPLIER,CShipment::SCOPE_USER_TO_US]);
        }

        $datatable->addCondition('shopId',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $val = \Monkey::app()->repoFactory->create('Shipment')->findOne([$row['id']]);

            $row["DT_RowId"] = $val->printId();
            $row['id'] = $val->printId();
            $row['shipmentInvoiceNumber']='<button style="width: 200px ; height:32px;"  onclick="openShipmentDetail(' . $val->shipmentInvoiceNumber . ')" class="btn btn-light"><i class="fa fa-list-alt" aria-hidden="true"></i> ' . $val->shipmentInvoiceNumber . '</button>';
            $row['shop'] = \Monkey::app()->repoFactory->create('Shop')->findOne([$row['shopId']])->name;
            $row['carrier'] = $val->carrier->name;
            $row['dateInvoice']=$val->dateInvoice;
            $res = \Monkey::app()->dbAdapter->query('select sum(realShipmentPrice) as total ,count(id) as totalShipment  from Shipment WHERE shipmentInvoiceNumber="'.$val->shipmentInvoiceNumber.'"',[])->fetchAll();
            foreach ($res as $result) {
                $impFat = $result['total'];
                $totalShipment=$result['totalShipment'];
            }
            $row['impFat']=money_format('%.2n',$impFat) . ' &euro;';
            $row['iva']=money_format('%.2n', $impFat/100*22) . ' &euro;';
            $row['totFat']=money_format('%.2n',$impFat+($impFat/100*22)) . ' &euro;';
            $row['totalShipment']=$totalShipment;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}