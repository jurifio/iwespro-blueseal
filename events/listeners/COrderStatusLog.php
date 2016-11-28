<?php

namespace bamboo\events\listeners;

use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\events\AEventListener;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class COrderStatusLog extends CLogging
{
    public function run($eventName)
    {
        $userId = $this->getParameter('userId');
        $time = $this->getParameter('time');
        if (!$time) $time = date('Y-m-d H:i:s');
        $order = $this->getParameter('order');
        if (!$order) $order = $this->getParameter('orderLine');
        $value = $this->getParameter('status');
        if (!$value) {
            $value = $order->status;
        }
        $this->insertLogRow($eventName, $userId, $value, $order->getEntityName(), $order->printId(), $time);
    }
}