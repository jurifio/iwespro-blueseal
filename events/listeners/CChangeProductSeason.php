<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CChangeProductSeason extends CLogging
{
    public function work($eventName)
    {
        $season = $this->getParameter('seasonId');
        if ($season) {
            $p = $this->getParameter('Product');
            $user = $this->getParameter('user');
            if ($user) $userId = $user->id;
            else $userId = $this->getParameter('userId');
            $time = $this->getParameter('time');

            $this->insertLogRow($eventName, $userId, $season, $p->getEntityName(), $p->printId(), $time);
        }
    }
}