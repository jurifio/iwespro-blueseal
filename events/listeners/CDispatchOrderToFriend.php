<?php

namespace bamboo\blueseal\events\listeners;

use bamboo\core\base\CObjectCollection;
use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\COrderLine;
use bamboo\export\order\COrderExport;


/**
 * Class CDispatchOrderToFriend
 * @package bamboo\blueseal\events\listeners
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CDispatchOrderToFriend extends AEventListener
{

    var $success = "ORD_FRND_ORDSNT";
    var $fail = "ORD_FRND_PYD";

    public function work($e) {
        if(!$e instanceof CEventEmitted) throw new BambooException('Event is not an event');
        $shopOrderLines = [];
        if($e->getEventData('orderLineIds')) {
            foreach ($e->getEventData('orderLineIds') as $orderLineId) {
                $orderLine = $this->app->repoFactory->create('OrderLine')->findOneByStringId($orderLineId);
                $shopOrderLines[$orderLine->shopId][] = $orderLine;
            }
        } elseif($e->getEventData('orderLineId')) {
            $orderLine = $this->app->repoFactory->create('OrderLine')->findOneByStringId($e->getEventData('orderLineId'));
            $shopOrderLines[$orderLine->shopId] = $orderLine;
        } elseif($e->getEventData('orderLines')) {
            foreach ($e->getEventData('orderLines') as $orderLine) {
                $shopOrderLines[$orderLine->shopId][] = $orderLine;
            }
        }
        foreach ($shopOrderLines as $shopId => $orderLines) {
            $this->dispatch($shopId,$orderLines);
        }
    }

    public function dispatch($shopId, $orderLines) {
        $orderExport = new COrderExport($this->app);
        $shop = $this->app->repoFactory->create('Shop')->findOneByStringId($shopId);
        try {
            $this->report('Working Shop ' . $shop->name . ' Start', 'Found ' . count($orderLines) . ' to send');
            if ($shop->orderExport == 1 && count($orderLines) >0 ) {
                $orderExport->exportOrderFileForFriend($shop, $orderLines);
            }
            if (isset($shop->referrerEmails) && count($orderLines) >0 ) {
                $orderExport->sendMailForOrderNotification($shop, $orderLines);
            }
            $this->report('Working Shop ' . $shop->name . ' End', 'Export ended');
        } catch(\Throwable $e){
            $this->error( 'Working Shop ' . $shop->name . ' End', 'ERROR Sending Lines',$e);
        }
    }
}