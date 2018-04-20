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

class CEditorialPlanDetailListAjaxController extends AAjaxController
{

    public function post()
    {
        $editorialPlanId = \Monkey::app()->router->request()->getRequestData('id');

        //trovi il piano editoriale
        /** @var ARepo $ePlanRepo */
        $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');

        /** @var CEditorialPlan $editorialPlan */
        $editorialPlan = $ePlanRepo->findOneBy(['id'=>$editorialPlanId]);

        /** @var CObjectCollection $editorialDetails */
        $editorialDetails = $editorialPlan->editorialPlanDetail;
        $data = [];
        $i = 0;
        /** @var \bamboo\domain\entities\CEditorialPlanDetail $singleDetail */

        foreach ($editorialDetails as $singleDetail) {
            $data[$i]["id"] = $singleDetail->id;
            $data[$i]["title"] = $singleDetail->title;
            $data[$i]["start"] = $singleDetail->startEventDate;
            $data[$i]["end"] = $singleDetail->endEventDate;
            $data[$i]["description"] = $singleDetail->description;
            $data[$i]["argument"] = $singleDetail->editorialPlanArgumentId;
            $data[$i]["argumentName"]= $singleDetail->editorialPlanArgument->titleArgument;
            $data[$i]["photoUrl"] = $singleDetail->photoUrl;
            $data[$i]["status"] = $singleDetail->status;
            $data[$i]["note"] = $singleDetail->note;
            $data[$i]["socialId"] = $singleDetail->socialId;
            $data[$i]["socialName"] = $singleDetail->editorialPlanSocial->name;
            $i++;
        }

        return json_encode($data);
    }
}