<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 06/04/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;

class CEditorialPlanDetailListAjaxController extends AAjaxController
{

    public function get()
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
            $data[$i]["title"] = $singleDetail->title;
            $data[$i]["start"] = $singleDetail->startEventDate;
            $data[$i]["end"] = $singleDetail->endEventDate;
            $i++;
        }

        return json_encode($data);
    }
}