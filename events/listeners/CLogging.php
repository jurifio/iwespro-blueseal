<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\domain\entities\CUser;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CLogging extends AEventListener
{
    /**
     * @var CUser
     */
    protected $user;

    protected $backtrace = null;
    protected $params = null;

    /**
     * @param $e
     * @throws BambooException
     */
    public function work($e)
    {
        if(!$e instanceof CEventEmitted) throw new BambooException('Event is not an event');
        $this->backtrace = $e->getBacktrace();
        $this->params = $e->getEventData();
        $eventName = $e->getEventName();
        $this->user = (!$e->getUserId()) ? \Monkey::app()->getUser() : \Monkey::app()->repoFactory->create('User')->findOneBy(['id' => $e->getUserId()]);

        $value = $this->getParameter('value');
        $entityName = $this->getParameter('entityName');
        $stringId = $this->getParameter('stringId');
        $time = $this->getParameter('time');
        $actionName = $this->getParameter('actionName');
        $userId = $this->getParameter('userId');

        $this->insertLogRow($eventName, $userId, $value, $entityName, $stringId, $time, $actionName);
    }

    protected function insertLogRow($eventName, $userId = null, $value = null, $entityName = null, $stringId = null, $time = null, $actionName = null) {
        if (!$actionName) $actionName = $this->getActionByClass();
        if (!$eventName) throw new BambooException('Il nome dell\'evento è obbligatorio per l\'inserimento del record nei log');
        if (!is_numeric($userId)) {
            $user = $this->user;
            if (!$user) throw new BambooException('Non è stato possibile trovare l\'id utente per il logging');
            $userId = $user->id;
        }

        $logR = \Monkey::app()->repoFactory->create('Log');
        $newLog = $logR->getEmptyEntity();
        $newLog->entityName = $entityName;
        $newLog->stringId = $stringId;
        $newLog->eventName = $eventName;
        $newLog->userId = $userId;
        $newLog->actionName = $actionName;
        $newLog->eventValue = $value;
        $newLog->backtrace = $this->getBacktraceLog();
        if ($time) $newLog->time = $time;
        $newLog->insert();
    }

    protected function getBacktrace($type = 'array') {
        if ('array' == $type) return $this->backtrace;
        if ('string' == $type) {
            $bt = $this->backtrace;
            $class = explode("\\", $bt['class']);
            $class = $class[count($class)-1];
            return $class . $bt['type'] . $bt['function'];
        }
    }

    protected function getBacktraceLog() {
        return $this->getBacktrace('string');
    }

    protected function getActionByClass(){
        return substr((new \ReflectionClass($this))->getShortName(), 1);
    }

    /**
     * Se una entity viene eseguita in asincrono vengono passati in un array la sua classe e le sue chiavi
     * @param $obj
     */

    protected function parseEntities($obj) {
          if ($obj instanceof AEntity) {
             $EntityName = $obj->getEntityName();
             $keys = $obj->printId();
          } else {
              $className = explode('\\', $obj['className']);
             $EntityName = substr($className[count($className)-1], 1);
             $keys = $obj['stringId'];
          }
          return ['entityName' => $EntityName, 'stringId' => $keys];
    }

    protected function getEntity($var) {
        if ($var instanceof AEntity) return $var;
        elseif (is_array($var) && 2 == count($var)){
                if (array_key_exists('entityName', $var)) {
                    $entity = $var['entityName'];
                    $stringId = $var['stringId'];
                } else if (array_key_exists('className', $var)) {
                    $parsed = $this->parseEntities($var);
                    $entity = $parsed['entityName'];
                    $stringId = $parsed['stringId'];
                }
            return \Monkey::app()->repoFactory->create($entity)->findOneByStringId($stringId);
        } else {
            return null;
        }
    }

    protected function getParameter($name = null) {
        $param = $this->getParam($name);
        $res = $this->getEntity($param);
        if (null === $res) {
            return $param;
        }
        return $res;
    }


    protected function getParam($name = null) {
        if (null == $name) return $this->params;
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        } else {
            return null;
        }
    }
}