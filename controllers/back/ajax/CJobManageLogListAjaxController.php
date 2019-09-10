<?php


namespace bamboo\controllers\back\ajax;


/**
 * Class CJobManageNameController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/09/2019
 * @since 1.0
 */
class CJobManageLogListAjaxController extends AAjaxController
{
    public function get()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $id = $data['id'];
            $jobExecution = \Monkey::app()->dbAdapter->query('SELECT max(id)as threadId  FROM JobExecution where jobId = ?', [$id])->fetchAll();
            if (empty($jobExecution)) {
                $res = 'non ci sono Esecuzioni per il job: ' . $id;
                return $res;
            } else {

                foreach ($jobExecution as $execution) {
                    $threadId = $execution['threadId'];
                }
                    $jobLog = \Monkey::app()->repoFactory->create('JobLog')->findBy(['jobId' => $id, 'jobExecutionId' => $threadId]);
                    if (!empty($jobLog)) {
                        $res = '<table align="center">';
                        $res .= '<tr><th>severity</th><th>subject</th><th>content</th><th>timestamp</th><th>context</th></tr>';
                        foreach ($jobLog as $logs) {
                            $res .= '<tr><td>' . $logs->severity . '</td><td>' . $logs->subject . '</td><td>' . $logs->content . '</td><td>' . $logs->timestamp . '</td><td>' . $logs->context . '</td></tr>';
                        }
                        $res.'</table>';
                    }

                }


            return $res;
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CJobManageLogListAjaxController', 'ERROR', 'Get Log List ', $e->getMessage());
            $res = 'Errore tipo di errore:<br>' . $e->getMessage();
        }
        return $res;

    }

    public function post()
    {

    }

    public function put()
    {

    }

    public function delete()
    {

    }

}