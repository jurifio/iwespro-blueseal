<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CChangeProductseason extends CLogging
{
    public function run($eventName)
    {
        $season = $this->getParameter('seasonId');
        if ($season) {
            $p = $this->getParamters('Product');
            $user = $this->getParamters('user');
            if ($user) $userId = $user->id;
            else $userId = $this->getParamters('userId');
            $time = $this->getParamter('time');

            $this->insertLogRow($eventName, $userId, $season, $p->getEntityName(), $p->printId(), $time);
        }
    }
}