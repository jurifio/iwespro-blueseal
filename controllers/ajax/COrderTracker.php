<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\CShipmentRepo;

/**
 * Class CGetPermissionsForUser
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class COrderTracker extends AAjaxController
{
    use TMySQLTimestamp;

    public function post()
    {
	    $orderId = $this->app->router->request()->getRequestData('orderId');
	    $langId = $this->app->router->request()->getRequestData('langId');
	    $carrierId = $this->app->router->request()->getRequestData('carrierId');
	    $trackingNumber = $this->app->router->request()->getRequestData('tracking');
        $order = $this->app->repoFactory->create('Order')->findOneByStringId($orderId);
        $lang = $this->app->repoFactory->create('Lang')->findOneByStringId($langId);

        /** @var CShipmentRepo $shipmentRepo */
        $shipmentRepo = $this->app->repoFactory->create('Shipment');
        $shipment = $shipmentRepo->newOrderShipmentToClient($carrierId,$trackingNumber,$this->time(),$order);

        $order->note = $order->note." Tracking ".$shipment->carrier->name.": ".$shipment->trackingNumber. ' Spedito: '.date('Y-m-d');
        $order->update();
        $this->app->orderManager->changeStatus($order,'ORD_SHIPPED');

        $to = [$order->user->email];
        $this->app->mailer->prepare('shipmentclient','no-reply', $to,[],[],['order'=>$order,'orderId'=>$orderId,'shipment'=>$shipment,'lang'=>$lang->lang]);
        $res = $this->app->mailer->send();
        if($res) return 'ok';
        return false;
    }
}