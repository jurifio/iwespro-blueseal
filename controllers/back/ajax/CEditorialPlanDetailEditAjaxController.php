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
use bamboo\domain\entities\CEditorialPlanArgument;
use bamboo\domain\entities\CEditorialPlanDetail;
use \bamboo\utils\time\STimeToolbox;

class CEditorialPlanDetailEditAjaxController extends AAjaxController
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
        $isVisibleNote = $data['isVisibleNote'];
        $note = $data['note'];
        $description = $data['description'];
        $isVisibleDescription = $data['isVisibleDescription'];
        $photoUrl = $data['photoUrl'];
        $isVisiblePhotoUrl = $data['isVisiblePhotoUrl'];
        $bodyEvent = $data['bodyEvent'];
        $isVisibleBodyEvent = $data['isVisibleBodyEvent'];
        $status = $data['status'];
        $socialId = $data['socialId'];
        $editorialPlanId = $data['editorialPlanId'];
        $startEventDate = STimeToolbox::FormatDateFromDBValue($startEventDate, 'Y-m-d h:m:s');
        $endEventDate = STimeToolbox::FormatDateFromDBValue($endEventDate, 'Y-m-d h:m:s');
        $editorialPlanDetailId = $data['editorialPlanDetailId'];
        $notifyEmail = $data['notifyEmail'];

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

        $editorialPlanDetail->isEventVisible = $isEventVisible;
        if (!empty($startEventDate)) {
            $editorialPlanDetail->startEventDate = $startEventDate;
        }
        if (!empty($endEventDate)) {
            $editorialPlanDetail->endEventDate = $endEventDate;
        }
        if (!empty($argument)) {
            $editorialPlanDetail->editorialPlanArgumentId = $argument;
        }

        $editorialPlanDetail->isVisibleEditorialPlanArgument = $isVisibleEditorialPlanArgument;
        if (!empty($description)) {
            $editorialPlanDetail->description = $description;
        }

        $editorialPlanDetail->isVisibleDescription = $isVisibleDescription;
        if (!empty($photoUrl)) {
            $editorialPlanDetail->photoUrl = $photoUrl;
        }

        $editorialPlanDetail->isVisiblePhotoUrl = $isVisiblePhotoUrl;
        if (!empty($status)) {
            $editorialPlanDetail->status = $status;
        }
        if (!empty($bodyEvent)) {
            $editorialPlanDetail->bodyEvent = $bodyEvent;
        }
        if (!empty($note)) {
            $editorialPlanDetail->note = $note;
        }

        $editorialPlanDetail->isVisibleNote = $isVisibleNote;

        $editorialPlanDetail->isVisibleBodyEvent = $isVisibleBodyEvent;
        if (!empty($socialId)) {
            $editorialPlanDetail->socialId = $socialId;
        }
        if (!empty($editorialPlanId)) {
            $editorialPlanDetail->editorialPlanId = $editorialPlanId;
        }


        // eseguo la commit sulla tabella;

        $editorialPlanDetail->update();

        $res = "Evento Azione  Piano modificato con successo!";
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
        $subject = "Modifica Evento Azione Piano Editoriale";
        $message = "Modifica Evento Azione Piano Editoriale<p>";
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


        return $res;
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $title = $data['title'];

        $argument = $data['argument'];
        $note = $data['note'];
        $description = $data['description'];
        $photoUrl = $data['photoUrl'];
        $status = $data['status'];
        $socialId = $data['socialId'];
        $editorialPlanId = $data['editorialPlanId'];
        $notifyEmail = "yesNotify";


        $editorialPlanDetailId = $data['editorialPlanDetailId'];
        /** @var CRepo $editorialPlanDetail */
        $editorialPlanDetail = \Monkey::app()->repoFactory->create('editorialPlanDetail');

        /** @var CEditorialPlanDetail $editorial */
        $editorial = $editorialPlanDetail->findOneBy(['id' => $editorialPlanDetailId]);
        $editorial->delete();
        $res = "  Evento Azione Piano Editoriale Cancellato";
        /** @var ARepo $shopRepo */
        $ePlanRepo = \Monkey::app()->repoFactory->create('EditorialPlan');

        /** @var CEditorialPlan $editorialPlan */
        $editorialPlan = $ePlanRepo->findOneBy(['id' => $editorialPlanId]);

        /** var ARepo $editorialPlanArgumentRepo */
        $editorialPlanArgumentRepo = \Monkey::app()->repoFactory->create('EditorialPlanArgument');

        /** @var CEditorialPlanArgument $editorialPlanArgument */
        $editorialPlanArgument = $editorialPlanArgumentRepo->findOneBy(['id' => $argument]);
        $argumentName = $editorialPlanArgument->titleArgument;

        $shopId = $editorialPlan->shop->id;
        $shopEmail = $editorialPlan->shop->referrerEmails;
        /** @var Ceditorial $to */
        $to = $shopEmail;
        $editorialPlanName = $editorialPlan->name;
        $subject = "Cancellazione Evento Azione Piano Editoriale";
        $message = "Cancellazione Evento Azione Editoriale<p>";
        $message .= "Title:" . $title . "<p>";
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

            // if (ENV == 'dev') return false;
            /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
            $emailRepo = \Monkey::app()->repoFactory->create('Email');
            if (!is_array($to)) {
                $to = [$to];
            }
            $emailRepo->newMail('Iwes IT Department <it@iwes.it>', $to, [], [], $subject, $message);
        }
        return $res;
    }
}