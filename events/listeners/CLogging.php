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
        $entity = $this->getParam('entity');

        $run = true;
        $fieldControl = null;

        switch($eventName) {
            case "setReleaseDate":
                $fieldControl = 'releaseDate';
                break;
        }

        if ($fieldControl) {
            $entityToControl = \Monkey::app()->repoFactory->create($entity->getEntityName())->findOneByStringId($entity->getIds());
            $run = false;
            if ($entityToControl) $run = ($entityToControl->{$fieldControl}) ? false : true ;
        }

    }

    protected function appendToLog($eventName, $value, $) {
        if (($value) && ($entity) && ($run)) {
            $entityName = $entity->getEntityName();
            $stringId = implode('-', $entity->getIds());
            $newLog = $logR->getEmptyEntity();
            $newLog->entityName = $entityName;
            $newLog->stringId = $stringId;
            $newLog->eventName = $eventName;
            $newLog->eventValue = $value;
            $newLog->insert();
        }
        return $this->getParam();
    }


}