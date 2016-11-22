<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\exceptions\BambooException;
use bamboo\core\application\AApplication;

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

    private $backtrace = null;

    public function __construct(AApplication $app, $params = [], $backtrace = [])
    {
        parent::__construct($app, $params);
        $this->user = \Monkey::app()->getUser();
        $this->backtrace = $backtrace;
    }

    /**
     * @param $eventName
     */
    public function run($eventName)
    {
        $value = $this->getParam('value');
        $entityName = $this->getParam('entityName');
        $stringId = $this->getParam('stringId');
        $time = $this->getParam('time');
        $actionName = $this->getParam('actionName');
        $user = $this->
        $userId =

        $this->insertLogRow($eventName, $actionName, $value, $entityName, $stringId, $time, $actionName);
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
}