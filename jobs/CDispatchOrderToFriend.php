<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
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
class CDispatchOrderToFriend extends ACronJob
{

    var $success = "ORD_FRND_ORDSNT";
    var $fail = "ORD_FRND_PYD";

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shops = $this->app->repoFactory->create('Shop')->findAll();
        $query = "SELECT * from OrderLine where `status` in ('ORD_FRND_PYD') AND shopId = ? ";
        $orderExport = new COrderExport($this->app);
        /** @var CRepo $orderLineRepo */
        $orderLineRepo = $this->app->repoFactory->create('OrderLine');

        foreach($shops as $shop){
            $lines = new CObjectCollection();
            try {
                $lines = $orderLineRepo->em()->findBySql($query, [$shop->id]);
                $this->report('Working Shop ' . $shop->name . ' Start', 'Found ' . count($lines) . ' to send');
                if ($shop->orderExport == 1 && count($lines) >0 ) {
                    $orderExport->exportOrderFileForFriend($shop, $lines);
                }
                if (isset($shop->referrerEmails) && count($lines) >0 ) {
                    $orderExport->sendMailForOrderNotification($shop, $lines);
                }
                $this->report('Working Shop ' . $shop->name . ' End', 'Export ended');
                $this->app->dbAdapter->beginTransaction();
                foreach($lines as $line){
                    try {

                        $orderLine = $this->app->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id, 'orderId' => $line->orderId]);
                        $orderLine->status = $this->success;
	                    $orderLine->update();

                        \Monkey::app()->eventManager->newTrigger('friendOrderShipped',
                            [
                                'order' => $orderLine,
                                'status' => $this->success
                            ]);

                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                    //$this->app->dbAdapter->update('OrderLine',['status'=>$this->success],["id"=>$line->id,"orderId"=>$line->orderId]);
                }
                $this->app->dbAdapter->commit();
            } catch(\Throwable $e){
                $this->error( 'Working Shop ' . $shop->name . ' End', 'ERROR Sending Lines',$e);
                $this->app->dbAdapter->beginTransaction();
                foreach($lines as $line){
                    try {

                        $orderLine = $this->app->repoFactory->create("OrderLine")->findOneBy(['id' => $line->id, 'orderId' => $line->orderId]);
                        $orderLine->status = $this->fail;
	                    $orderLine->update();

                        \Monkey::app()->eventManager->newTrigger('friendOrderPayd',
                            [
                                'order' => $orderLine,
                                'status' => $this->fail
                            ]);

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