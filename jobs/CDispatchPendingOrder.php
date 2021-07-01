<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\export\order\COrderExport;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CDispatchPendingOrder extends ACronJob
{

    var $success = "ORD_WAIT";
    var $fail = "ORD_PENDING";

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shops = \Monkey::app()->repoFactory->create('Shop')->findBy(['isActive' => 1]);
        $query = "SELECT * from `OrderLine` ol join `Order` o on ol.orderId=o.id where ol.`status` in ('ORD_PENDING') AND o.dateOrder >'2021-06-20' AND shopId = ?";
        $orderExport = new COrderExport($this->app);
        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');

        foreach($shops as $shop) {
            $lines = new CObjectCollection();
            try {
                $lines = $orderLineRepo->em()->findBySql($query,[$shop->id]);
                $this->report( 'Working Shop ' . $shop->name . ' Start', 'Found ' . count($lines) . ' to send');
                if (isset($shop->referrerEmails) && count($lines) > 0) {
                    $orderExport->sendMailPendingOrder($shop,$lines);
                }
                $this->report('Working Shop ' . $shop->name . ' End','Export ended');
                \Monkey::app()->repoFactory->beginTransaction();
                foreach ($lines as $line) {
                    try {

                        $orderLine = \Monkey::app()->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id,'orderId' => $line->orderId]);
                        $orderLineRepo->updateStatus($orderLine,$this->success);

                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                }
                \Monkey::app()->repoFactory->commit();
            } catch (\Throwable $e) {
                $this->error('Working Shop ' . $shop->name . ' End','ERROR Sending Lines',$e);
                \Monkey::app()->repoFactory->beginTransaction();
                foreach ($lines as $line) {
                    try {

                        $orderLine = \Monkey::app()->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id,'orderId' => $line->orderId]);
                        $orderLineRepo->updateStatus($orderLine,$this->fail);

                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                }
                \Monkey::app()->repoFactory->commit();
            }
        }
    }
}