<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CSaveDescription
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
class CSaveDescription extends AAjaxController
{
    public function put()
    {
        $datas = $this->data;
        \Monkey::dump($datas);
        throw new \Exception();
        $order = $this->app->repoFactory->create('Order')->findOne([$datas['order_id']]);
        $this->app->orderManager->changeStatus($order,$datas['order_status']);
	    $order->note = $datas['order_note'];
	    $order->update();
        return true;
    }
}