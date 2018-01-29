<?php

namespace bamboo\controllers\back\ajax;

use Aws\DynamoDb\Model\Attribute;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooORMInvalidEntityException;
use bamboo\core\exceptions\BambooORMReadOnlyException;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterTemplate;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CNewsletterRepo;


/**
 * Class CProductSizeGroupManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNewsletterUserManage extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function post()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $name = $data['name'];
        $fromEmailAddressId = $data['fromEmailAddressId'];
        $sendAddressDate = $data['sendAddressDate'];
        $newsletterEmailListId = $data['newsletterEmailListId'];
        $newsletterTemplateId = $data['newsletterTemplateId'];
        $subject = $data['subject'];
        $dataDescription = $data['dataDescription'];
        $preCompiledTemplate = $data['preCompiledTemplate'];
        $campaignName = $data['campaignName'];
        $campaignId = $data['campaignId'];
        $newsletterEventId = $data['newsletterEventId'];
        $dateCampaignStart = $data['dateCampaignStart'];
        $dateCampaignFinish = $data['dateCampaignFinish'];
        $newsletterEventName = $data['newsletterEventName'];
        if (empty($campaignId)) {

            /** @var CRepo $newsletterCampaignRepo */

            $newsletterCampaignRepo = \Monkey::app()->repoFactory->create('NewsletterCampaign');

            /** @var CNewsletterCampaign $newsletterCampaign */

            $newsletterCampaign = $newsletterCampaignRepo->findOneBy(['name' => $campaignName]);

            if (empty($newsletterCampaign)) {
                $newsletterCampaignInsert = \Monkey::app()->repoFactory->create('NewsletterCampaign')->getEmptyEntity();
                $newsletterCampaignInsert->name = $campaignName;
                $newsletterCampaignInsert->dateCampaignStart = $dateCampaignStart;
                $newsletterCampaignInsert->dateCampaignFinish = $dateCampaignFinish;
                $newsletterCampaignInsert->smartInsert();
                $newsletterCampaign = $newsletterCampaignRepo->findOneBy(['name' => $campaignName]);
                $campaignId = $newsletterCampaign->id;
                $res = "Campagna inserita con successo";
                if (empty($newsletterEventId)) {
                    /** var CRepo $newsletterEventRepo */
                    $newsletterEventRepo = \Monkey::app()->repoFactory->create('NewsletterEvent');
                    /** var CNewsletterEvent $newsletterEvent */
                    $newsletterEvent = $newsletterEventRepo->findOneBy(['name' => $newsletterEventName]);
                    if (empty($newsletterEvent)) {
                        $newsletterEventInsert = \Monkey::app()->repoFactory->create('NewsletterEvent')->getEmptyEntity();
                        $newsletterEventInsert->name = $newsletterEventName;
                        $newsletterEventInsert->newsletterCampaignId = $campaignId;
                        $newsletterEventInsert->smartInsert();
                        $newsletterEvent = $newsletterEventRepo->findOneBy(['name' => $newsletterEventName]);
                        $newsletterEventId = $newsletterEvent->id;
                        $res = "Evento della Campagna inserita con successo";
             } else {
                        $res = "Attenzione esiste gia un evento della Campagna con lo stesso nome ";
             }
                }
            } else {
                $res = "Attenzione esiste già una campagna con lo stesso nome";
            }
        }

        /** @var CRepo $newsletterTemplateRepo */
        $newsletterTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate');

        /** @var CNewsletterTemplate $newsletterTemplate */
        $newsletterTemplate = $newsletterTemplateRepo->findOneBy(['template' => $newsletterTemplateId]);
        $newsletterTemplateId = $newsletterTemplate->id;

        /** @var CRepo $newsletterUserRepo */
        $newsletterUserRepo = \Monkey::app()->repoFactory->create('Newsletter');

        /** @var CNewsletterUser $newsletterUser */
        $newsletterUser = $newsletterUserRepo->findOneBy(['name' => $name]);


        if (empty($newsletterUser)) {
            //se la variabile non è istanziata inserisci in db

            /** @var CNewsletterUser $newsletterUserInsert */
            $newsletterUserInsert = \Monkey::app()->repoFactory->create('Newsletter')->getEmptyEntity();
            //popolo la tabella

            $newsletterUserInsert->name = $name;
            $newsletterUserInsert->fromEmailAddressId = $fromEmailAddressId;
            $newsletterUserInsert->sendAddressDate = $sendAddressDate;
            $newsletterUserInsert->newsletterEmailListId = $newsletterEmailListId;
            $newsletterUserInsert->newsletterTemplateId = $newsletterTemplateId;
            $newsletterUserInsert->subject = $subject;
            $newsletterUserInsert->dataDescription = $dataDescription;
            $newsletterUserInsert->preCompiledTemplate = $preCompiledTemplate;
            $newsletterUserInsert->newsletterCampaignId = $campaignId;
            $newsletterUserInsert->newsletterEventId = $newsletterEventId;
            // eseguo la commit sulla tabella;

            $newsletterUserInsert->smartInsert();

            $res = "Newsletter inserita con successo!";

        } else {
            //Se hai trovato qualcosa allora restituitsci messaggio di errore
            $res = "Esiste già una newsletter con lo stesso nome";
        }

        return $res;


    }
    public function secToTime($init){
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;

        return "$hours:$minutes:$seconds";
    }

    public function put()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $idNewsletterUser = $data['idNewsletterUser'];

        /** @var CNewsletter $newsletterUser */
        $newsletterUser = \Monkey::app()->repoFactory->create('Newsletter')->findOneBy(['id' => $idNewsletterUser]);

        $newsletterId = $newsletterUser->id;

        $checkNewsletterUser = $newsletterUser;

        if (empty($checkNewsletterUser)) {
            $res = "<p style='color:red'>la Newsletter  che stai cercando di inviare non esiste</p>";

        } else if (!empty($newsletterId)) {
            // ottengo i valori dalla tabella newsletter

            $fromEmailAddressId = $newsletterUser->fromEmailAddressId;
            $sendAddressDate = $newsletterUser->sendAddressDate;
            $newsletterEmailListId = $newsletterUser->newsletterEmailListId;
            $subject = $newsletterUser->subject;
            $dataDescription = $newsletterUser->dataDescription;
            $preCompiledTemplate = $newsletterUser->preCompiledTemplate;
            $newsletterCampaignId = $newsletterUser->newsletterCampaignId;
            $newsletterEventId = $newsletterUser->newsletterEventId;


            //  ottengo le informazioni del sender;

            /** @var CEmailAddress $emailAddress */

            $emailAddress = \Monkey::app()->repoFactory->create('EmailAddress')->findOneBy(['id' => $fromEmailAddressId]);
            $fromEmailAddress = $emailAddress->id;
            $checkEmailAddress = $emailAddress;

            if (empty($checkEmailAddress)) {
                $res = "<p style='color:red'>il sender che stai cercando di selezionare non esiste</p>";
            } else if (!empty($fromEmailAddress)) {
                $from = $emailAddress->id;
            }

            /** @var  $CNewsletterEmailList $newsletterEmailList */
            //ottengo  la query sql da applicare sul bacino di utenza selezionato

            $newsletterEmailList = \Monkey::app()->repoFactory->create('NewsletterEmailList')->findOneBy(['id' => $newsletterEmailListId]);
            $newsletterEmail = $newsletterEmailList->id;
            $checkNewsletterEmailList = $newsletterEmailList;

            if (empty($checkNewsletterUser)) {
                $res = "<p style='color:red'>il filtro per il gruppo selezionato  che stai cercando non esiste</p>";
            } else if (!empty($newsletterEmail)) {
                $filterSql = $newsletterEmailList->sql;
                $newsletterGroupId = $newsletterEmailList->newsletterGroupId;

            }

            // ottento la query sql del gruppo

            /** @var CNewsletterGroup $newsletterGroup */

            $newsletterGroup = \Monkey::app()->repoFactory->create('NewsletterGroup')->findOneBy(['id' => $newsletterGroupId]);
            $group = $newsletterGroup->id;
            $checkNewsletterGroup = $newsletterGroup;

            if (empty($checkNewsletterGroup)) {
                $res = "<p style='color:red'>il  gruppo selezionato  che stai cercando non esiste</p>";
            } else if (!empty($group)) {
                $sqlDefault = $newsletterGroup->sql;

            }
            $sql = $sqlDefault . " " . $filterSql;


        }
        // popolo l'array dei destinatari
        $to = [];
        $indirizzi = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $fineciclo = 0;
        $verificafineciclo = 1;
        foreach ($indirizzi as $val) {
            $to[] = $val["email"];
            $fineciclo = $fineciclo + 1;
            $verificafineciclo = $verificafineciclo + 1;

        }
        $fineciclo = $fineciclo + 1;
        //creo l'entità email
        /** @var CEmailRepo $emailRepo */
        $emailRepo = \Monkey::app()->repoFactory->create('Email');


        $emailRepo->newMail($from, $to, [], [], $subject, $preCompiledTemplate, null, $newsletterId, 'mailGun', 'false');
        if ($fineciclo === $verificafineciclo) {

        $res = "Email Generate  ";

        return res;
    }
    }



}