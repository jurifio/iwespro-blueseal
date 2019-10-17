<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\repositories\COrderRepo;

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

    public function get() {
        try {
            $osOC = \Monkey::app()->repoFactory->create('OrderStatus')->findAll();
            $ret = [];
            $ret['statuses'] = [];
            foreach($osOC as $v) {
                $trans = $v->orderStatusTranslation->findOneByKey('langId', 1);
                $ret['statuses'][$v->id] = $trans->title;
            }
            return json_encode($ret);
        } catch(BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessate();
        }
    }

    /**
     * @return bool|string
     */
    public function put()
    {
        $dba = \Monkey::app()->dbAdapter;
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            /** @var COrderRepo $oR */
            $oR = \Monkey::app()->repoFactory->create('Order');
            $datas = $this->data;
            $orders = new CObjectCollection();

            if (array_key_exists('order_id', $datas)) {
                $orders = $oR->findBy(['id' => $datas['order_id']]);
            } else {
                $orders = $oR->findBySql('SELECT id FROM `Order` WHERE id in ( ? )', [implode(',', $datas['orders'])]);
            }

            foreach($orders as $order) {
                $oR->updateStatus($order, $datas['order_status']);

                $order->note = $datas['order_note'] ?? null;
                $order->shipmentNote = $datas['order_shipmentNote'] ?? null;
                $order->isShippingToIwes=$datas['isShippingToIwes'] ?? null;
                $order->update();
            }
            \Monkey::app()->repoFactory->commit();
            return true;
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}