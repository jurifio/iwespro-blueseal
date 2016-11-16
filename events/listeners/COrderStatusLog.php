<?php

namespace bamboo\events\listeners;

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
        $time = $this->getParam('time');
        if (!$time) $time = date('Y-m-d H:i:s');
        $order = $this->getParam('order');
        if (!$order) $order = $this->getParam('orderLine');
        if ($order) {
            $value = $this->getParam('status');
            if (!$value) $order->status;
            $this->insertLogRow($eventName, null, $order->getEntityName(), $order->printId(), $time);
        }
    }
}