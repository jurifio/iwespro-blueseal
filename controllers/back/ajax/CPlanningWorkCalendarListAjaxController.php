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

class CPlanningWorkCalendarListAjaxController extends AAjaxController
{

    public function post()
    {

        $planningWork=\Monkey::app()->repoFactory->create('PlanningWork')->findAll();
        $data = [];
        $i = 0;
        $p=1;
        foreach ($planningWork as $singleDetail) {

                $data[$i]["allShops"] = $p;
                $data[$i]["id"] = $singleDetail->id;
                $data[$i]["title"] = $singleDetail->title;
                $data[$i]["start"] = $singleDetail->startDateWork;
                $data[$i]["end"] = $singleDetail->endDateWork;
                switch ($singleDetail->planningWorkStatusId) {
                    case "1":
                        $status = "chiamata";
                        break;
                    case "2":
                        $status = "in Programmazione";
                        break;
                    case "3":
                        $status = "Completato";
                        break;
                    case "4":
                        $status = "Fatturato";
                        break;
                }
                $data[$i]["status"] = $status;
                $data[$i]["planningWorkStatusId"] = $singleDetail->planningWorkStatusId;
                $data[$i]["request"] = $singleDetail->request;
                $data[$i]["solution"] = $singleDetail->solution;
                $data[$i]["billRegistryClientId"] = $singleDetail->billRegistryClientId;
                $brc=\Monkey::app()->repoFactory->create('BillRegistryClient')->findOneBy(['id'=>$singleDetail->billRegistryClientId]);
                if($brc) {
                    $client = $brc->companyName;
                }else {
                    $client = 'Non Assegnato';
                }
                $data[$i]["companyName"] = $client;
                $data[$i]["shopId"] = $singleDetail->shopId;
                $data[$i]["percentageStatus"] = $singleDetail->percentageStatus;




                $i++;
            }



        return json_encode($data);
    }
}