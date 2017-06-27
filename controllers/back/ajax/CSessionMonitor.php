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
	    $CORES = 5;
	    $load = sys_getloadavg();
	    $ret['load']['m1'] = $load[0] / $CORES;
	    $ret['load']['m5'] = $load[1] / $CORES;
	    $ret['load']['m15'] = $load[2] / $CORES;

        $ret['traffic'] = $this->app->dbAdapter->query("SELECT
                                                      count(DISTINCT sid) as sessions,
                                                      count(DISTINCT userId) as users
                                                    FROM ActivityLog
                                                    WHERE creationDate > date_sub(now(), INTERVAL 1 MINUTE)", [])->fetchAll()[0];
        return json_encode($ret);


		return $this->app->dbAdapter->query("	SELECT COUNT(DISTINCT sid) conto
												FROM UserSession us
												WHERE lastUpdate > (current_timestamp() - INTERVAL 30 SECOND ) OR
                                                		creationDate > (current_timestamp() - INTERVAL 30 SECOND )", []
											)->fetchAll(\PDO::FETCH_COLUMN, 'conto')[0];
	}
}