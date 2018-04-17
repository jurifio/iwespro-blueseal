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

class CEditorialPlanDetailAddAjaxController extends AAjaxController
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


        /** @var CRepo $editorialPlanDetailRepo */
        $editorialPlanDetailRepo = \Monkey::app()->repoFactory->create('EditorialPlanDetail');

        /** @var CEditorialPlanDetail $editorialPlanDetail */
        $editorialPlanDetail = $editorialPlanDetailRepo->findOneBy(['title' => $title]);


        if (empty($editorialPlanDetail)) {
            //se la variabile non è istanziata inserisci in db

            /** @var CEditorialPlanDetail $editorialPlanDetailInsert */
            $editorialPlanDetailInsert = \Monkey::app()->repoFactory->create('EditorialPlanDetail')->getEmptyEntity();
            //popolo la tabella

            $editorialPlanDetailInsert->title = $title;
            $editorialPlanDetailInsert->startEventDate = $startEventDate;
            $editorialPlanDetailInsert->endEventDate = $endEventDate;
            $editorialPlanDetailInsert->argument = $argument;
            $editorialPlanDetailInsert->description = $description;
            $editorialPlanDetailInsert->photoUrl = $photoUrl;
            $editorialPlanDetailInsert->status = $status;
            $editorialPlanDetailInsert->note = $note;
            $editorialPlanDetailInsert->socialId = $socialId;
            $editorialPlanDetailInsert->editorialPlanId = $editorialPlanId;

            // eseguo la commit sulla tabella;

            $editorialPlanDetailInsert->smartInsert();

            $res = "Dettaglio Piano Editoriale inserito con successo!";

        } else {
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già una Dettaglio piano Editoriale con lo stesso nome";
        }

        return $res;
    }
}