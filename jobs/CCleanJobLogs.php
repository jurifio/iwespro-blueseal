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
class CCleanJobLogs extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $this->deleteJobLogs();
    }

    /**
     * @param int $days
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function deleteJobLogs($days = 10)
    {
        $this->app->dbAdapter->query("DELETE FROM JobLog", [])->execute();


		$this->app->cacheService->getCache('entities')->flush();
        $this->report('deleted Job Logs', 'deleted ');
    }
}