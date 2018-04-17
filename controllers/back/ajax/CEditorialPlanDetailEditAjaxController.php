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
use \bamboo\utils\time\STimeToolbox;

class CEditorialPlanDetailEditAjaxController extends AAjaxController
{

    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $title = $data['title'];
        $startEventDate = $data['start'];
        $endEventDate = $data['end'];
        $argument = $data['argument'];
        $note = $data['note'];
        $description = $data['description'];
        $photoUrl = $data['photoUrl'];
        $status = $data['status'];
        $socialId = $data['socialId'];
        $editorialPlanId = $data['editorialPlanId'];
        $startEventDate = STimeToolbox::FormatDateFromDBValue($startEventDate,'Y-m-d h:m:s');
        $endEventDate =STimeToolbox::FormatDateFromDBValue($endEventDate,'Y-m-d h:m:s');
        $editorialPlanDetailId = $data['editorialPlanDetailId'];

       /* $startEventDate = STimeToolbox::FormatDateFromDBValue($startEventDate, 'Y-m-d h:m:s');
        $endEventDate = STimeToolbox::FormatDateFromDBValue($endEventDate, 'Y-m-d h:m:s');*/



        /** @var CRepo $editorialDetailRepo */
        $editorialDetailRepo = \Monkey::app()->repoFactory->create('EditorialPlanDetail');

        /** @var CEditorialPlanDetail $editorialPlanDetail */
        $editorialPlanDetail = $editorialDetailRepo->findOneBy(['id' => $editorialPlanDetailId]);


        //se la variabile non è istanziata inserisci in db

        /** @var CEditorialPlanDetail $editorialPlanDetail */

        //popolo la tabella

        if (!empty($title)) {
            $editorialPlanDetail->title = $title;
        }
        if (!empty($startEventDate)) {
            $editorialPlanDetail->startEventDate = $startEventDate;
        }
        if (!empty($endEventDate)) {
            $editorialPlanDetail->endEventDate = $endEventDate;
        }
        if (!empty($argument)) {
            $editorialPlanDetail->argument = $argument;
        }
        if (!empty($description)) {
            $editorialPlanDetail->description = $description;
        }
        if (!empty($photoUrl)) {
            $editorialPlanDetail->photoUrl = $photoUrl;
        }
        if (!empty($status)) {
            $editorialPlanDetail->status = $status;
        }
        if (!empty($socialId)) {
            $editorialPlanDetail->socialId = $socialId;
        }
        if (!empty($editorialPlanId)) {
            $editorialPlanDetail->editorialPlanId = $editorialPlanId;
        }



        // eseguo la commit sulla tabella;

        $editorialPlanDetail->update();

        $res = "Dettaglio Piano modificato con successo!";


        return $res;
    }
}