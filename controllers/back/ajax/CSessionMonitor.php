<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\controllers\back\ajax;

/**
 * Class CChangeLineShop
 * @package bamboo\app\controllers
 */
class CSessionMonitor extends AAjaxController
{
	/**
	 * @return bool
	 * @throws \bamboo\core\exceptions\RedPandaException
	 */
	public function get()
	{
	    $CORES = 10;
	    $load = sys_getloadavg();
	    $ret['load']['m1'] = round($load[0] / $CORES * 100,2);
	    $ret['load']['m5'] = round($load[1] / $CORES * 100,2);
	    $ret['load']['m15'] = round($load[2] / $CORES * 100,2);

        $ret['traffic'] = $this->app->dbAdapter->query("SELECT
                                                      count(DISTINCT sid) as sessions,
                                                      count(DISTINCT userId) as users
                                                    FROM ActivityLog
                                                    WHERE creationDate > date_sub(now(), INTERVAL 1 MINUTE)", [])->fetchAll()[0];

        $ret['job'] = $this->app->dbAdapter->query("SELECT
                                                      count(id) as conto
                                                    FROM Job
                                                    WHERE isRunning = 1", [])->fetchAll()[0]['conto'];

        $ret['eventQueue'] = \Monkey::app()->eventManager->getQueueLen();

        return json_encode($ret);
	}
}