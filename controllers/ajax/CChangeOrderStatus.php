<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\COrder;

/**
 * Class CChangeOrderStatus
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
class CChangeOrderStatus extends AAjaxController
{
    public function put()
    {
        try {
            /** @var COrder $oR */
            $oR = \Monkey::app()->repoFactory->create('Order');
            $datas = $this->data;
            $order = $oR->findOne([$datas['order_id']]);
            $oR->updateStatus($order, $datas['order_status']);
            $order->note = $datas['order_note'];
            $order->update();
            return true;
        } catch (BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}