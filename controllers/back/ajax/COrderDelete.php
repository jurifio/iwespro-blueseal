<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\CShipment;

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
class COrderDelete extends AAjaxController
{
    use TMySQLTimestamp;

    public function post()
    {
        $orderId = $this->app->router->request()->getRequestData('orderId');
        $langId = $this->app->router->request()->getRequestData('langId');
        $productsIds = $this->app->router->request()->getRequestData('productsIds');
        $products = [];
        foreach ($productsIds as $productIds) {
            $products[] = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($productIds);
        }
        $order = $this->app->repoFactory->create('Order')->findOneByStringId($orderId);
        $lang = $this->app->repoFactory->create('Lang')->findOneByStringId($langId);

        $order->note = $order->note . " Cancellato: " . date('Y-m-d');
        $order->update();
        $this->app->orderManager->changeStatus($order, 'ORD_FR_CANCEL');

        $coupon = \Monkey::app()->repoFactory->create('Coupon')->createCouponFromType(37,$order->user->printId());

        $to = [$order->user->email];
        $this->app->mailer->prepare('deleteorderclient', 'no-reply', $to, [], [],
            ['order' => $order,
                'orderId' => $orderId,
                'products' => $products,
                'coupon' => $coupon,
                'lang' => $lang->lang]);
        $res = $this->app->mailer->send();
        if ($res) return 'ok';
        return false;
    }
}