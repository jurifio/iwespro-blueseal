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
use bamboo\core\email\CEmail;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanSocial;
use \bamboo\utils\time\STimeToolbox;

class CEditorialPlanDetailAddAjaxController extends AAjaxController
{

    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $title = $data['title'];
        $isEventVisible = $data['isEventVisible'];
        $startEventDate = $data['start'];
        $endEventDate = $data['end'];
        $argument = $data['argument'];
        $isVisibleEditorialPlanArgument = $data['isVisibleEditorialPlanArgument'];
        $isVisiblePhotoUrl = $data['isVisiblePhotoUrl'];
        $bodyEvent = $data['bodyEvent'];
        $isVisibleBodyEvent = $data['isVisibleBodyEvent'];
        $note = $data['note'];
        $isVisibleNote = $data['isVisibleNote'];
        $description = $data['description'];
        $isVisibleDescription = $data['isVisibleDescription'];
        $photoUrl = $data['photoUrl'];
        $status = $data['status'];
        $socialId = $data['socialId'];
        $editorialPlanId = $data['editorialPlanId'];
        $startEventDate = STimeToolbox::FormatDateFromDBValue($startEventDate, 'Y-m-d h:m:s');
        $endEventDate = STimeToolbox::FormatDateFromDBValue($endEventDate, 'Y-m-d h:m:s');
        $notifyEmail = $data['notifyEmail'];


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
            $editorialPlanDetailInsert->isEventVisible = $isEventVisible;
            $editorialPlanDetailInsert->startEventDate = $startEventDate;
            $editorialPlanDetailInsert->endEventDate = $endEventDate;
            $editorialPlanDetailInsert->argument = $argument;
            $editorialPlanDetailInsert->isVisibleEditorialPlanArgument = $isVisibleEditorialPlanArgument;
            $editorialPlanDetailInsert->description = $description;
            $editorialPlanDetailInsert->isVisibleDescription = $isVisibleDescription;
            $editorialPlanDetailInsert->photoUrl = $photoUrl;
            $editorialPlanDetailInsert->isVisiblePhotoUrl = $isVisiblePhotoUrl;
            $editorialPlanDetailInsert->status = $status;
            $editorialPlanDetailInsert->note = $note;
            $editorialPlanDetailInsert->isVisibleNote = $isVisibleNote;
            $editorialPlanDetailInsert->socialId = $socialId;
            $editorialPlanDetailInsert->bodyEvent =$bodyEvent;
            $editorialPlanDetailInsert->isVisibleBodyEvent =$isVisibleBodyEvent;
            $editorialPlanDetailInsert->editorialPlanId = $editorialPlanId;

            // eseguo la commit sulla tabella;

            $editorialPlanDetailInsert->smartInsert();

            $res = "Dettaglio Piano Editoriale inserito con successo!";
            /** @var ARepo $shopRepo */
            $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');

            /** @var CEditorialPlan $editorialPlan */
            $editorialPlan = $ePlanRepo->findOneBy(['id' => $editorialPlanId]);

            $shopId = $editorialPlan->shop->id;
            $shopEmail = $editorialPlan->shop->referrerEmails;
            /** var ARepo $editorialPlanArgumentRepo */
            $editorialPlanArgumentRepo = \Monkey::app()->repoFactory->create('EditorialPlanArgument');

            /** @var CEditorialPlanArgument $editorialPlanArgument */
            $editorialPlanArgument = $editorialPlanArgumentRepo->findOneBy(['id' => $argument]);
            $argumentName = $editorialPlanArgument->titleArgument;
            /** @var Ceditorial $to */
            $to = $shopEmail;
            $editorialPlanName = $editorialPlan->name;
            $subject = "Creazione Nuovo Dettaglio Piano Editoriale";
            $message = "Creazione Nuovo dettaglio Piano Editoriale<p>";
            $message .= "Title:" . $title . "<p>";
            $message .= "Data di Inizio:" . $startEventDate . "<p>";
            $message .= "Data di Fine:" . $endEventDate . "<p>";
            $message .= "Argomento:" . $argumentName . "<p>";
            $message .= "Descrizione:" . $description . "<p>";
            $message .= "Stato:" . $status . "<p>";
            $message .= "Note:" . $note . "<p>";
            /** @var ARepo $ePlanSocialRepo */
            $ePlanSocialRepo = \Monkey::app()->repoFactory->create('EditorialPlanSocial');
            /** @var CEditorialPlanSocial $editorialPlanSocial */
            $editorialPlanSocial = $ePlanSocialRepo->findOneBy(['id' => $socialId]);

            /** @var CObjectCollection $editorialPlanSocialName */
            $editorialPlanSocialName = $editorialPlanSocial->name . "<p>";
            $message .= "Media utilizzato:" . $editorialPlanSocialName . "<p>";
            $message .= "Piano Editoriale:" . $editorialPlanName . "<p>";


            if ($notifyEmail === "yesNotify") {

                if (ENV == 'dev') return false;
                /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                if (!is_array($to)) {
                    $to = [$to];
                }
                $emailRepo->newMail('Iwes IT Department <it@iwes.it>', $to, [], [], $subject, $message);
            }

        } else {
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già un Evento Azione per il  piano Editoriale con lo stesso nome";
        }


        return $res;
    }
}