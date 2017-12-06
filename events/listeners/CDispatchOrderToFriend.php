<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;
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

    public function work($e)
    {
        $this->report('DispatchOrderToFriendEvent', 'Starting', $e);
        if (!$e instanceof CEventEmitted) throw new BambooException('Event is not an event');
        $shopOrderLines = [];
        if ($e->getEventData('orderLineIds')) {
            $this->report('DispatchOrderToFriendEvent', 'found orderLineIds', $e);
            foreach ($e->getEventData('orderLineIds') as $orderLineId) {
                $orderLine = \Monkey::app()->repoFactory->create('OrderLine')->findOneByStringId($orderLineId);
                $shopOrderLines[$orderLine->shopId][] = $orderLine;
            }
        } elseif ($e->getEventData('orderLineId')) {
            $this->report('DispatchOrderToFriendEvent', 'found orderLineIds', $e);
            $orderLine = \Monkey::app()->repoFactory->create('OrderLine')->findOneByStringId($e->getEventData('orderLineId'));
            $shopOrderLines[$orderLine->shopId] = $orderLine;
        } elseif ($e->getEventData('orderLines')) {
            $this->report('DispatchOrderToFriendEvent', 'found orderLines', $e);
            foreach ($e->getEventData('orderLines') as $orderLine) {
                $shopOrderLines[$orderLine->shopId][] = $orderLine;
            }
        }
        $this->report('DispatchOrderToFriendEvent','dispatching lines',$shopOrderLines);
        foreach ($shopOrderLines as $shopId => $orderLines) {
            try{
                $this->dispatch($shopId, $orderLines);
            } catch (\Throwable $e) {
                $this->error('Cycling shops', 'what',$e);
            }

        }
    }

    public function dispatch($shopId, $orderLines)
    {
        $orderExport = new COrderExport(\Monkey::app());
        $shop = \Monkey::app()->repoFactory->create('Shop')->findOneByStringId($shopId);
        try {
            $this->report('Working Shop ' . $shop->name . ' Start', 'Found ' . count($orderLines) . ' to send');
            if ($shop->orderExport == 1 && count($orderLines) > 0) {
                $orderExport->exportOrderFileForFriend($shop, $orderLines);
            }
            if (isset($shop->referrerEmails) && count($orderLines) > 0) {
                $orderExport->sendMailForOrderNotification($shop, $orderLines);
            }
            $this->report('Working Shop ' . $shop->name . ' End', 'Export ended');
        } catch (\Throwable $e) {
            $this->error('Working Shop ' . $shop->name . ' End', 'ERROR Sending Lines', $e);
        }
    }
}