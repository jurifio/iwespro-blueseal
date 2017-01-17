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
        $shops = $this->app->repoFactory->create('Shop')->findAll();
        $query = "SELECT * from OrderLine where `status` in ('ORD_FRND_SNDING', 'ORD_ERR_SEND') AND shopId = ? ";
        $orderExport = new COrderExport($this->app);
        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = $this->app->repoFactory->create('OrderLine');

        foreach($shops as $shop){
            $lines = new CObjectCollection();
            try {
                $lines = $orderLineRepo->em()->findBySql($query, [$shop->id]);
                $this->report( 'Working Shop ' . $shop->name . ' Start', 'Found ' . count($lines) . ' to send');
                foreach ($lines as $line) {
                    if($line->friendRevenue == 0 || is_null($line->friendRevenue)) {
                        $line->friendRevenue = $this->app->repoFactory->create('ProductSku')->calculateFriendRevenue($line->productSku);
                        $line->update();
                        $this->app->eventManager->triggerEvent('orderLine.friendRevenue.update',['orderLineId'=>$line->printId()]);
                    }
                }
                if ($shop->preOrderExport == 1 && count($lines) >0 ) {
                    $orderExport->exportPrefileForFriend($shop, $lines);
                }
                if (isset($shop->referrerEmails) && count($lines) >0 ) {
                    $orderExport->sendMailForFriendConfirmation($shop, $lines);
                }
                $this->report( 'Working Shop ' . $shop->name . ' End', 'Export ended');
                $this->app->dbAdapter->beginTransaction();
                foreach($lines as $line){
                    try {

                        $orderLine = $this->app->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id, 'orderId' => $line->orderId]);
                        $orderLineRepo->updateStatus($orderLine, $this->success);

                        /**$userId = $orderLine->shop->user->id;

                        \Monkey::app()->eventManager->triggerEvent('friendSendRequestSuccess',
                            [
                                'order' => $orderLine,
                                'status' => $this->success,
                                'userId' => $userId
                            ]);*/

                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                    //$this->app->dbAdapter->update('OrderLine',['status'=>$this->success],["id"=>$line->id,"orderId"=>$line->orderId]);
                }
                $this->app->dbAdapter->commit();
            } catch(\Throwable $e){
                $this->error('Working Shop ' . $shop->name . ' End', 'ERROR Sending Lines',$e);
                $this->app->dbAdapter->beginTransaction();
                foreach($lines as $line){
                    try {

                        $orderLine = $this->app->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id, 'orderId' => $line->orderId]);
                        $orderLineRepo->updateStatus($orderLine, $this->fail);

                        /**$userId = $orderLine->shop->user->id;
                        \Monkey::app()->eventManager->triggerEvent('friendSendRequestFail',
                            [
                                'order' => $orderLine,
                                'status' => $this->fail,
                                'userId' => $userId
                            ]);*/

                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                    //$this->app->dbAdapter->update('OrderLine',['status'=>$this->fail],["id"=>$line->id,"orderId"=>$line->orderId]);
                }
                $this->app->dbAdapter->commit();
            }
        }
    }
}