<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\controllers\back\ajax\AAjaxController;
use bamboo\domain\entities\CShipment;
use bamboo\utils\time\STimeToolbox;

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
class CJobListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                    id,
                    scope,
                    name,
                    protocol,
                    host,
                    command,
                    defaultArgs,
                    if(isActive = 1, 'sì','no') as isActive,
                    if(isRunning = 1, 'sì','no') as isRunning,
                    if(manualStart = 1, 'sì','no') as manualStart,
                    if(manualKill = 1, 'sì','no') as manualKill,
                    secondsToLive,
                    username,
                    password,
                    minute,
                    hour,
                    mday,
                    month,
                    wday,
                    logFile,
                    notificationSetup,
                    notificationEmail,
                    lastExecution,
                    lastUpdate,
                    isDebug
                FROM Job";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $res = $this->app->dbAdapter->query($datatable->getQuery(false, true), $datatable->getParams())->fetchAll();
        $count = $this->app->repoFactory->create('Job')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Job')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($res as $raw) {
            $row = $raw;
            $job = $this->app->repoFactory->create('Job')->findOne([$raw['id']]);
            $row["DT_RowId"] = $job->printId();
            $row["DT_RowClass"] = $job->isActive ? "" : "grey";
            $row["DT_RowClass"] = $job->manualStart ? "yellow" : $row["DT_RowClass"];
            $row["DT_RowClass"] = $job->isRunning ? "green" : $row["DT_RowClass"];

            $response['data'][] = $row;
        }

        return json_encode($response);
    }
}