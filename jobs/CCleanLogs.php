<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CCleanLogs extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->deleteJobLogs();
    }

    /**
     * @param int $days
     */
    public function deleteJobLogs($days = 20)
    {
        $rows = 0;
        while ($res = $this->app->dbAdapter->query(" SELECT count(id) AS conto
                                              FROM JobLog
                                              WHERE timestamp < (NOW() - INTERVAL ? DAY)",
                                                [$days])->fetchAll()[0]['conto']
                        > 0) {
            $limit = 10000;
            $this->report( 'Working', 'res: '.$res.' limit: '.$limit);
            $rows += $this->app->dbAdapter->query("DELETE FROM JobLog WHERE timestamp < (NOW() - INTERVAL ? DAY) LIMIT ?", [$days,$limit], true)->countAffectedRows();
            $this->report('Working', 'rows: '.$rows);
        }
		$this->app->cacheService->getCache('entities')->flush();
        $this->report('deleted Job Logs', 'deleted ' . $rows . ' rows');
    }
}