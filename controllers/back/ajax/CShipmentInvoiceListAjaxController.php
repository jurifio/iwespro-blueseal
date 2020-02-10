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
                     s.dateInvoice as dateInvoice
                
                  
                FROM Shipment s 
                  join Carrier c on s.carrierId = c.id
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
            $row['shipmentInvoiceNumber']=$val->shipmentInvoiceNumber;
            $row['shop'] = \Monkey::app()->repoFactory->create('Shop')->findOne([$row['shopId']])->name;
            $row['carrier'] = $val->carrier->name;


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