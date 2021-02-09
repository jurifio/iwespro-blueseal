<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 06/04/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;


use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanArgument;
use bamboo\domain\entities\CEditorialPlanSocial;

class CPlanningWorkCalendarFilterAjaxController extends AAjaxController
{

    /**
     * @return string
     * @throws \Throwable
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $planningWorkStatus = $data['planningWorkStatusId'];
        $planningWork = \Monkey::app()->repoFactory->create('PlanningWork')->findAll();
        $data = [];
        $i = 0;
        $p = 1;
        foreach ($planningWorkStatus as $statuses) {
            foreach ($planningWork as $singleDetail) {
                if ($statuses == $singleDetail->planningWorkStatusId) {
                    $data[$i]["allShops"] = $p;
                    $data[$i]["id"] = $singleDetail->id;
                    $data[$i]["title"] = $singleDetail->title;
                    $data[$i]["start"] = $singleDetail->startDateWork;
                    $data[$i]["end"] = $singleDetail->endDateWork;
                    switch ($singleDetail->planningWorkStatusId) {
                        case "1":
                            $status = "Richiesta";
                            break;
                        case "2":
                            $status = "in Programmazione";
                            break;
                        case "3":
                            $status = "in Avanzamento";
                            break;
                        case "4":
                            $status = "Completato";
                            break;
                        case "5":
                            $status = "Completato da Modulo";
                            break;
                    }
                    $data[$i]["status"] = $status;
                    $data[$i]["planningWorkStatusId"] = $singleDetail->planningWorkStatusId;
                    $data[$i]["planningWorkTypeId"] = $singleDetail->planningWorkTypeId;
                    $data[$i]["request"] = $singleDetail->request;
                    $data[$i]["solution"] = $singleDetail->solution;
                    $data[$i]["billRegistryClientId"] = $singleDetail->billRegistryClientId;
                    $ata[$i]["notifyEmail"] = $singleDetail->notifyEmail;
                    $brc = \Monkey::app()->repoFactory->create('BillRegistryClient')->findOneBy(['id' => $singleDetail->billRegistryClientId]);
                    if ($brc) {
                        $client = $brc->companyName;
                    } else {
                        $client = 'Non Assegnato';
                    }
                    $data[$i]["companyName"] = $client;
                    $data[$i]["shopId"] = $singleDetail->shopId;
                    $data[$i]["percentageStatus"] = $singleDetail->percentageStatus;


                    $i++;
                }
            }
        }


        return json_encode($data);
    }


    public function get()
    {

    $res=[];
        /** @var CObjectCollection $planningWorkStatus */
        $planningWorkStatus = \Monkey::app()->repoFactory->create('PlanningWorkStatus')->findAll();
        $user = \Monkey::app()->getUser()->id;
        $allShops = \Monkey::app()->getUser()->hasPermission('allShops');
        if ($allShops == true) {

            /** @var CPlanningWorkStatus $statuses */
            foreach ($planningWorkStatus as $statuses) {
                $res[]=['id'=>$statuses->id, "name"=> $statuses->name, "color"=> $statuses->color];

            }
        }


        return json_encode($res);
    }
}