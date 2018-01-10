<?php

namespace bamboo\events\listeners;

use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;
use bamboo\utils\time\STimeToolbox;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CSupplierShippingTimeLog extends CLogging
{
    public function work($eventName)
    {
        if(!$eventName instanceof CEventEmitted) throw new BambooException('Event is not an event');
        $this->backtrace = $eventName->getBacktrace();
        $this->params = $eventName->getEventData();
        $userId = $eventName->getUserId();

        $time = STimeToolbox::DbFormattedDateTime($this->getParameter('time'));
        if (!$time) $time = date('Y-m-d H:i:s');
        $order = $this->getParameter('order');
        if (!$order) $order = $this->getParameter('orderLine');
        $value = $this->getParameter('status');
        if (!$value) {
            $value = $order->status;
        }
        $entityName = $order->getEntityName();
        $stringId = $order->printId();
        $logR = \Monkey::app()->repoFactory->create('Log');
        $lC = $logR->findBy(['entityName' => $entityName, 'stringId' => $stringId], '', 'ORDER BY time desc');
        $check = $lC->getFirst();
        if (!$check || $check->eventValue != $value) {
            if($userId == 0) $userId = 1;
            $this->insertLogRow($eventName->getEventName(), $userId, $value, $entityName, $stringId, $time);
        }
    }
}