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
class CDispatchPreorderToFriend extends ACronJob
{

    var $success = "ORD_FRND_SENT";
    var $fail = "ORD_ERR_SEND";

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shops = \Monkey::app()->repoFactory->create('Shop')->findbY(['isActive' => 1]);
        $query = "SELECT * from OrderLine where `status` in ('ORD_FRND_SNDING', 'ORD_ERR_SEND') AND shopId = ? ";
        $orderExport = new COrderExport($this->app);
        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');

        foreach($shops as $shop){
            $lines = new CObjectCollection();
            try {
                $lines = $orderLineRepo->em()->findBySql($query, [$shop->id]);
                $this->report( 'Working Shop ' . $shop->name . ' Start', 'Found ' . count($lines) . ' to send');
                foreach ($lines as $line) {
                    if($line->friendRevenue == 0 || is_null($line->friendRevenue)) {
                        $line->friendRevenue = \Monkey::app()->repoFactory->create('ProductSku')->calculateFriendRevenue($line->productSku);
                        $line->update();
                    }
                }
                if ($shop->preOrderExport == 1 && count($lines) >0 ) {
                    $orderExport->exportPrefileForFriend($shop, $lines);
                }
                if (isset($shop->referrerEmails) && count($lines) >0 ) {
                    $orderExport->sendMailForFriendConfirmation($shop, $lines);
                }
                $this->report( 'Working Shop ' . $shop->name . ' End', 'Export ended');
                \Monkey::app()->repoFactory->beginTransaction();
                foreach($lines as $line){
                    try {

                        $orderLine = \Monkey::app()->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id, 'orderId' => $line->orderId]);
                        $orderLineRepo->updateStatus($orderLine, $this->success);

                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                }
                \Monkey::app()->repoFactory->commit();
            } catch(\Throwable $e){
                $this->error('Working Shop ' . $shop->name . ' End', 'ERROR Sending Lines',$e);
                \Monkey::app()->repoFactory->beginTransaction();
                foreach($lines as $line){
                    try {

                        $orderLine = \Monkey::app()->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id, 'orderId' => $line->orderId]);
                        $orderLineRepo->updateStatus($orderLine, $this->fail);

                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                }
                \Monkey::app()->repoFactory->commit();
            }
        }
    }
}