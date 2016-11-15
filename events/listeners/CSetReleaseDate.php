<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CSetReleaseDate extends AEventListener
{
    public function run($eventName)
    {
      #throw new BambooException('triggerato in ' . $eventName);
    }
}