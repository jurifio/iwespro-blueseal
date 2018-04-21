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
        $res = [];
        $i = 0;
        /** @var \bamboo\domain\entities\CEditorialPlanDetail $singleDetail */

        foreach ($editorialDetails as $singleDetail) {
            $res[$i]["id"] = $singleDetail->id;
            $res[$i]["title"] = $singleDetail->title;
            $res[$i]["start"] = $singleDetail->startEventDate;
            $res[$i]["end"] = $singleDetail->endEventDate;
            $res[$i]["description"] = $singleDetail->description;
            $res[$i]["argument"] = $singleDetail->editorialPlanArgumentId;
            $res[$i]["argumentName"]= $singleDetail->editorialPlanArgument->titleArgument;
            $res[$i]["photoUrl"] = $singleDetail->photoUrl;
            $res[$i]["status"] = $singleDetail->status;
            $res[$i]["note"] = $singleDetail->note;
            $res[$i]["socialId"] = $singleDetail->socialId;
            $res[$i]["socialName"] = $singleDetail->editorialPlanSocial->name;
            $res[$i]['titleEditorialPlan'] = $editorialPlanName;
            $i++;
        }

        return json_encode($res);
    }
}