<?php

namespace bamboo\business\carrier;

use bamboo\domain\entities\CShipment;

class CDhlStopWatchHandler {
    //@var $startTimes array The start times of the StopWatches
    private static $startTimes = array();

    //Start the timer
    //@param $timerName string The name of the timer
    //@return void
    public static function start($timerName = 'default') {
        self::$startTimes[$timerName] = microtime(true);
    }

    //Get the elapsed time in seconds
    //@param $timerName string The name of the timer to start
    //@return float The elapsed time since start() was called
    public static function elapsed($timerName = 'default') {
        return round((microtime(true) - self::$startTimes[$timerName]) * 1000);
    }
}