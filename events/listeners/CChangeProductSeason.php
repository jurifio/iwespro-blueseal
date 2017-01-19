<?php

namespace bamboo\events\listeners;

use bamboo\core\events\CEventEmitted;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CChangeProductSeason extends CLogging
{
    public function work($e)
    {   /** @var  CEventEmitted $e */
        $eventName = $e->getEventName();
        $userId = $e->getUserId();
        $season = $this->getParameter('seasonId');
        if ($season) {
            $p = $this->getParameter('Product');
            $time = $this->getParameter('time');
            $this->insertLogRow($eventName, $userId, $season, $p->getEntityName(), $p->printId(), $time);
        }
    }
}