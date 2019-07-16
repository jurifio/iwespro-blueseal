<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;

/**
 * Class CJobListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CJobLogListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                    j.id as id,
                    jl.id as  `idtransaction`,
                    j.scope as scope,
                    j.name as name,
                    j.lastExecution as lastExecution,
                    jl.severity as typeReport,
                    jl.subject as subject,
                    jl.content as content,
                    `jl`.`timestamp` as `timestamp`
                FROM Job j join JobLog jl on jl.jobId =j.id WHERE `timestamp`>= CURDATE() - INTERVAL 3 DAY AND `timestamp` <= CURDATE()";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $res = $this->app->dbAdapter->query($datatable->getQuery(false, true), $datatable->getParams())->fetchAll();
        $count = \Monkey::app()->repoFactory->create('Job')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('Job')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($res as $raw) {
            $row = $raw;
            $job = \Monkey::app()->repoFactory->create('Job')->findOne([$raw['id']]);
            $row["DT_RowId"] = $job->printId();


            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}