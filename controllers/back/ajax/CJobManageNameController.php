<?php


namespace bamboo\controllers\back\ajax;


/**
 * Class CJobManageNameController
 * @package bamboo\controllers\back\ajax
 * @author Iwes Team <it@iwes.it>, 10/09/2019
 * @copyright (c) Iwes snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since 1.0
 */
class CJobManageNameController extends AAjaxController
{
    public function get()
    {

    }

    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();
            $name = $data["name"];
            $id = $data['id'];
            $priority=$data['priority'];
            $job = \Monkey::app()->repoFactory->create('Job')->findOneBy(['id' => $id]);
            $job->name = $name;
            $job->priority=$priority;
            $job->update();
            \Monkey::app()->applicationLog('CJobManageNameController', 'Report', 'update', 'Change name and priority Job id :'.$id.'in '.$name );
            $res='Aggiornamento eseguito con Successo';
        } catch(\Throwable $e) {
            \Monkey::app()->applicationLog('CJobManageNameController', 'ERROR', 'update', $e->getMessage());
            $res='Errore tipo di errore:<br>'.$e->getMessage();
        }
        return $res;
    }

    public function put()
    {

    }

    public function delete()
    {

    }

}