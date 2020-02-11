<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CShipmentInvoiceDetailAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/02/2020
 * @since 1.0
 *
 */
class CShipmentInvoiceDetailAjaxController extends AAjaxController
{
    public function get()
    {
        $shipmentDetail=[];
       $invoice = $this -> app -> router -> request() -> getRequestData('invoice');
        $res=\Monkey::app()->repoFactory->create('Shipment')->findBy(['shipmentInvoiceNumber'=>$invoice]);
        $res = \Monkey::app()->dbAdapter->query('select remoteShopShipmentId, sum(realShipmentPrice) as total from Shipment WHERE shipmentInvoiceNumber="'.$invoice.'" GROUP BY shipmentInvoiceNumber,remoteShopShipmentId',[])->fetchAll();
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');

        foreach ($res as $result) {
            $total=$result['total'];
$shop=$shopRepo->findOneBy(['id'=>$result->remoteShopShipmentId]);
$shopName=$shop->name;
            $imp=money_format('%.2n',$total) . ' &euro;';
            $iva=money_format('%.2n', $total/100*22) . ' &euro;';
            $totFat=money_format('%.2n',$total+($total/100*22)) . ' &euro;';
            $shipmentDetail[] = ['shipmentId' => $result->id,
                'imp' =>  $imp,
                'iva' => $iva,
                'totFat' => $totFat,
                'shop'=>$shopName
                ];
        }

        return json_encode($shipmentDetail);
    }
}