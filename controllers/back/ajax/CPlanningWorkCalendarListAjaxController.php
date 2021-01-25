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

        foreach ($planningWork as $singleDetail) {
            if($p==1) {
                $data[$i]["allShops"] = $p;
                $data[$i]["id"] = $singleDetail->id;
                $data[$i]["title"] = $singleDetail->title;
                $data[$i]["start"] = $singleDetail->startDateWork;
                $data[$i]["end"] = $singleDetail->endDateWork;
                $data[$i]["isEventVisible"] = $singleDetail->isEventVisible;
                $data[$i]["description"] = $singleDetail->description;
                $data[$i]["isVisibleDescription"] = $singleDetail->isVisibleDescription;
                $data[$i]["argument"] = $singleDetail->editorialPlanArgumentId;
                $data[$i]["argumentName"] = $singleDetail->editorialPlanArgument->titleArgument;
                $data[$i]["isVisibleEditorialPlanArgument"] = $singleDetail->isVisibleEditorialPlanArgument;
                $data[$i]["photoUrl"] = $singleDetail->photoUrl;
                $data[$i]["isVisiblePhotoUrl"] = $singleDetail->isVisiblePhotoUrl;
                $data[$i]["linkDestination"] = $singleDetail->linkDestination;
                $data[$i]["facebookCampaignId"] = $singleDetail->facebookCampaignId;
                $data[$i]["groupInsertionId"] = $singleDetail->groupInsertionId;
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
                $data[$i]["bodyEvent"] = $singleDetail->bodyEvent;
                $data[$i]["isVisibleBodyEvent"] = $singleDetail->isVisibleBodyEvent;
                $data[$i]["note"] = $singleDetail->note;
                $data[$i]["isVisibleNote"] = $singleDetail->isVisibleNote;
                $data[$i]["socialId"] = $singleDetail->socialId;


                $data[$i]["socialName"] = $singleDetail->editorialPlanSocial->name;
                $data[$i]["color"] = $singleDetail->editorialPlanSocial->color;
                $data[$i]['titleEditorialPlan'] = $editorialPlanName;


                $i++;
            }else{
                if ($singleDetail->userId == $this->app->getUser()->getId()) {
                    $data[$i]["allShops"] = '1';
                    $data[$i]["id"] = $singleDetail->id;
                    $data[$i]["title"] = $singleDetail->title;
                    $data[$i]["start"] = $singleDetail->startEventDate;
                    $data[$i]["end"] = $singleDetail->endEventDate;
                    $data[$i]["isEventVisible"] = $singleDetail->isEventVisible;
                    $data[$i]["description"] = $singleDetail->description;
                    $data[$i]["isVisibleDescription"] = $singleDetail->isVisibleDescription;
                    $data[$i]["argument"] = $singleDetail->editorialPlanArgumentId;
                    $data[$i]["argumentName"] = $singleDetail->editorialPlanArgument->titleArgument;
                    $data[$i]["isVisibleEditorialPlanArgument"] = $singleDetail->isVisibleEditorialPlanArgument;
                    $data[$i]["photoUrl"] = $singleDetail->photoUrl;
                    $data[$i]["isVisiblePhotoUrl"] = $singleDetail->isVisiblePhotoUrl;
                    $data[$i]["linkDestination"] = $singleDetail->linkDestination;
                    $data[$i]["facebookCampaignId"] = $singleDetail->facebookCampaignId;
                    $data[$i]["groupInsertionId"] = $singleDetail->groupInsertionId;
                    switch ($singleDetail->status) {
                        case "Draft":
                            $status = "Bozza";
                            break;
                        case "Approved":
                            $status = "Approvata";
                            break;
                        case "Rejected":
                            $status = "Rifiutata";
                            break;
                        case "Published":
                            $status = "Pubblicata";
                            break;
                    }
                    $data[$i]["status"] = $status;
                    $data[$i]["bodyEvent"] = $singleDetail->bodyEvent;
                    $data[$i]["isVisibleBodyEvent"] = $singleDetail->isVisibleBodyEvent;
                    $data[$i]["note"] = $singleDetail->note;
                    $data[$i]["isVisibleNote"] = $singleDetail->isVisibleNote;
                    $data[$i]["socialId"] = $singleDetail->socialId;


                    $data[$i]["socialName"] = $singleDetail->editorialPlanSocial->name;
                    $data[$i]["color"] = $singleDetail->editorialPlanSocial->color;
                    $data[$i]['titleEditorialPlan'] = $editorialPlanName;


                    $i++;
                }
            }
        }


        return json_encode($data);
    }
}