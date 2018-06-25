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

class CEditorialPlanDetailListFilteredAjaxController extends AAjaxController
{

    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $editorialPlanId=$data['id'];
        $editorialPlanSocialId = $data['socialId'];



        //trovi il piano editoriale
        /** @var ARepo $ePlanDetailRepo */
        $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');

        /** @var CEditorialPlan $editorialPlan */
        $editorialPlan = $ePlanRepo->findOneBy(['id'=>$editorialPlanId]);
        $editorialPlanName=$editorialPlan->name;

        /** @var CObjectCollection $editorialDetails */
        $editorialDetails = $editorialPlan->editorialPlanDetail;

        $p = \Monkey::app()->getUser()->hasPermission('allShops');
        /** @var \bamboo\domain\entities\CEditorialPlanDetail $singleDetail */
        if ($p == true) {
            $p = "1";
        } else {
            $p = "0";
        }

        $data = [];
        $i = 0;
        /** @var \bamboo\domain\entities\CEditorialPlanDetail $singleDetail */

        foreach ($editorialDetails as $singleDetail) {
        foreach($editorialPlanSocialId as $markerSocial){
            if($singleDetail->socialId ==$markerSocial) {
                $data[$i]["allShops"] = $p;
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
                $data[$i]["bodyEvent"]=$singleDetail->bodyEvent;
                $data[$i]["isVisibleBodyEvent"]=$singleDetail->isVisibleBodyEvent;
                $data[$i]["note"] = $singleDetail->note;
                $data[$i]["isVisibleNote"] = $singleDetail->isVisibleNote;
                $data[$i]["socialId"] = $singleDetail->socialId;




                $data[$i]["socialName"] = $singleDetail->editorialPlanSocial->name;
                $data[$i]["color"]=$singleDetail->editorialPlanSocial->color;
                $data[$i]['titleEditorialPlan'] = $editorialPlanName;
            }
        }
        }

        return json_encode($data);
    }
}