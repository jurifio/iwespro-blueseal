<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;

/**
 * Class CCleanLogs
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/05/2018
 * @since 1.0
 */
class CCleanApplicationLogs extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $this->deleteApplicationLogs();
    }

    /**
     * @param int $days
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function deleteApplicationLogs($days = 20)
    {
        $rows = 0;
        while ($res = $this->app->dbAdapter->query(" SELECT count(id) AS conto
                                              FROM ApplicationLog
                                              WHERE creationDate < (NOW() - INTERVAL ? DAY)",
                                                [$days])->fetchAll()[0]['conto']
                        > 0) {
            $limit = 10000;
            $this->report( 'Working', 'res: '.$res.' limit: '.$limit);
            $rows += $this->app->dbAdapter->query("DELETE FROM ApplicationLog WHERE creationDate < (NOW() - INTERVAL ? DAY) LIMIT ?", [$days,$limit], true)->countAffectedRows();
            $this->report('Working', 'rows: '.$rows);
        }
		$this->app->cacheService->getCache('entities')->flush();
        $this->report('deleted Application Logs', 'deleted ' . $rows . ' rows');
    }
}