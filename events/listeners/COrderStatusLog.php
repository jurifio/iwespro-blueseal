<?php

namespace bamboo\events\listeners;

use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class COrderStatusLog extends CLogging
{
    public function work($eventName)
    {
        if(!$eventName instanceof CEventEmitted) throw new BambooException('Event is not an event');
        $this->backtrace = $eventName->getBacktrace();
        $this->params = $eventName->getEventData();
        $userId = $eventName->getUserId();
        $time = $this->getParameter('time');
        if (!$time) $time = date('Y-m-d H:i:s');
        $order = $this->getParameter('order');
        if (!$order) $order = $this->getParameter('orderLine');
        $value = $this->getParameter('status');
        if (!$value) {
            $value = $order->status;
        }
        $this->insertLogRow($eventName->getEventName(), $userId, $value, $order->getEntityName(), $order->printId(), $time);
    }
}