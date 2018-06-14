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
 * Class CnewsletterUserManage
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
        $typeOperation=$data['typeOperation'];
        if($typeOperation==="1"){
            $name = $data['name'];
            $fromEmailAddressId = $data['fromEmailAddressId'];
            $sendAddressDate = $data['sendAddressDate'];
            $newsletterEmailListId = $data['newsletterEmailListId'];
            $newsletterTemplateId = "1";
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
      //      $newsletterTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate');

            /** @var CNewsletterTemplate $newsletterTemplate */
          //  $newsletterTemplate = $newsletterTemplateRepo->findOneBy(['template' => $newsletterTemplateId]);
         //   $newsletterTemplateId = $newsletterTemplate->id;

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
                /** var CRepo $newsletterUserRepo */
                $newsletterUserRepo = \Monkey::app()->repoFactory->create('Newsletter');
                /** var CNewsletterUserUpdate $newsletterUserUpdate **/
                $newsletterUserUpdate = $newsletterUserRepo->findOneBy(['name' => $name]);

                $newsletterUserId=$newsletterUserUpdate->id;
                $newsletterUserUpdate->newsletterCloneId = $newsletterUserId;
                $newsletterUserUpdate->update();
                //popolo la tabella

                $res = "Newsletter inserita con successo!";

            } else {
                //Se hai trovato qualcosa allora restituitsci messaggio di errore
                $res = "Esiste già una newsletter con lo stesso nome";
            }
        }else{
            $to=$data['toEmailAddressTest'];
            $message=$data['preCompiledTemplate'];
            $message = str_replace('emailunsuscriber',$to,$message);
            $subject=$data['subject'];
            $fromEmailAddressId=$data['fromEmailAddressId'];
          //  if (ENV == 'dev') return false;
            /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
            $emailRepo = \Monkey::app()->repoFactory->create('Email');
            if (!is_array($to)) {
                $to = [$to];

            }
            $emailRepo->newMail($fromEmailAddressId, $to, [], [], $subject, $message);
            $res="Test Inviato con successo";
        }

        return $res;


    }

    public function secToTime($init)
    {
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;

        return "$hours:$minutes:$seconds";
    }

    public function put()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $idNewsletterUser = $data['idNewsletterUser'];

        /** @var CNewsletterRepo $newsletterRepo */
        $newsletterRepo = \Monkey::app()->repoFactory->create('Newsletter');
        /** @var CNewsletter $newsletterUser */
        $newsletterUser = $newsletterRepo->findOneBy(['id' => $idNewsletterUser]);
        $isTest = true;
        return $newsletterRepo->sendNewsletterEmails($newsletterUser, $isTest);
    }


}