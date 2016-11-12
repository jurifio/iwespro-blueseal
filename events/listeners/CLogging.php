<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\exceptions\BambooException;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CLogging extends AEventListener
{
    public function run($eventName)
    {
        $logR = \Monkey::app()->repoFactory->create('Log');

        $value = $this->getParam('value');
        $entityName = $this->getParam('entityName');
        $stringId = $this->getParam('stringId');
        $time = $this->getParam('time');

        $this->insertLogRow($eventName, $value, $entityName, $stringId, $time);
    }

    protected function insertLogRow($eventName, $value, $entityName = null, $stringId = null, $time = null ) {
        if (!$eventName) throw new BambooException('Il nome dell\'evento è obbligatorio per l\'inserimento del record nei log');
        if (!$value) throw new BambooException('Il nome dell\'evento è obbligatorio per l\'inserimento del record nei log');

            $logR = \Monkey::app()->repoFactory->create('Log');
            $newLog = $logR->getEmptyEntity();
            $newLog->entityName = $entityName;
            $newLog->stringId = $stringId;
            $newLog->eventName = $eventName;
            $newLog->eventValue = $value;
            if ($time) $newLog->time = $time;
            $newLog->insert();
        return $this->getParam();
    }
}