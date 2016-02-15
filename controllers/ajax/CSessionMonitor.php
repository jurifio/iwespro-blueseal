<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\blueseal\controllers\ajax;

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
		return 0;
		return $this->app->dbAdapter->query("	SELECT COUNT(DISTINCT sid) conto
												FROM UserSession us
												WHERE lastUpdate > (current_timestamp() - INTERVAL 30 SECOND ) OR
                                                		creationDate > (current_timestamp() - INTERVAL 30 SECOND )", []
											)->fetchAll(\PDO::FETCH_COLUMN, 'conto')[0];
	}
}