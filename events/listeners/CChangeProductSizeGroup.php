<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CChangeProductSizeGroup extends CLogging
{
    public function work($eventName)
    {
        $groupSizeId = $this->getParameter('groupSizeId');
        if ($groupSizeId) {
            $p = $this->getParameter('product');
            $user = $this->getParameter('user');
            if ($user) $userId = $user->id;
            else $userId = $this->getParameter('userId');
            $time = $this->getParameter('time');

            $this->insertLogRow($eventName, $userId, $groupSizeId, $p->getEntityName(), $p->printId(), $time);
        }
    }
}