<?php

namespace bamboo\controllers\back\ajax;

use Aws\DynamoDb\Model\Attribute;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\email\CEmail;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooORMInvalidEntityException;
use bamboo\core\exceptions\BambooORMReadOnlyException;
use bamboo\domain\entities\CEmailAddress;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterInsertion;
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
        $typeOperation = $data['typeOperation'];
        if ($typeOperation === "1") {
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
            $newsletterInsertionId = $data['newsletterInsertion'];
            $newsletterInsertionName = $data['newsletterInsertionName'];

            if (empty($campaignId)) {

                /** @var CEmailAddress $ea */
                $ea = \Monkey::app()->repoFactory->create('EmailAddress')->findOneBy(['id'=>$fromEmailAddressId]);
                /** @var CNewsletterCampaign $newsletterCampaignInsert */
                $newsletterCampaignInsert = \Monkey::app()->repoFactory->create('NewsletterCampaign')->getEmptyEntity();
                $newsletterCampaignInsert->name = $campaignName;
                $newsletterCampaignInsert->newsletterShopId=$ea->newsletterShop->id;
                $newsletterCampaignInsert->dateCampaignStart = $dateCampaignStart;
                $newsletterCampaignInsert->dateCampaignFinish = $dateCampaignFinish;
                $newsletterCampaignInsert->smartInsert();

                $campaignId = $newsletterCampaignInsert->id;

                if (empty($newsletterEventId)) {

                    /** @var CNewsletterEvent $newsletterEventInsert */
                    $newsletterEventInsert = \Monkey::app()->repoFactory->create('NewsletterEvent')->getEmptyEntity();
                    $newsletterEventInsert->name = $newsletterEventName;
                    $newsletterEventInsert->newsletterCampaignId = $campaignId;
                    $newsletterEventInsert->smartInsert();

                    $newsletterEventId = $newsletterEventInsert->id;

                    if (empty($newsletterInsertionId)) {
                        /** @var CNewsletterInsertion $nIns */
                        $nIns = \Monkey::app()->repoFactory->create('NewsletterInsertion')->getEmptyEntity();
                        $nIns->name = $newsletterInsertionName;
                        $nIns->newsletterEventId = $newsletterEventId;
                        $nIns->smartInsert();

                        $newsletterInsertionId = $nIns->id;
                    }
                }
            }


            /** @var CNewsletter $newsletter */
            $newsletter = \Monkey::app()->repoFactory->create('Newsletter')->getEmptyEntity();

            $newsletter->name = $name;
            $newsletter->fromEmailAddressId = $fromEmailAddressId;
            $newsletter->sendAddressDate = $sendAddressDate;
            $newsletter->newsletterEmailListId = $newsletterEmailListId;
            $newsletter->newsletterTemplateId = $newsletterTemplateId;
            $newsletter->subject = $subject;
            $newsletter->dataDescription = $dataDescription;
            $newsletter->preCompiledTemplate = $preCompiledTemplate;
            $newsletter->newsletterCampaignId = $campaignId;
            $newsletter->newsletterEventId = $newsletterEventId;
            $newsletter->newsletterInsertionId = $newsletterInsertionId;
            $newsletter->smartInsert();

            /** @var CRepo $newsletterRepo */
            $newsletterRepo = \Monkey::app()->repoFactory->create('Newsletter');
            /** @var CNewsletter $newsletterUpdate */
            $newsletterUpdate = $newsletterRepo->findOneBy(['id' => $newsletter->id]);
            $newsletterUpdate->newsletterCloneId = $newsletter->id;
            $newsletterUpdate->update();


            $res = "Newsletter inserita con successo!";

        } else {
            $to = $data['toEmailAddressTest'];
            $message = $data['preCompiledTemplate'];
            $message = str_replace('emailunsuscriber', $to, $message);
            $subject = $data['subject'];
            $fromEmailAddressId = $data['fromEmailAddressId'];
            //  if (ENV == 'dev') return false;
            /** @var \bamboo\domain\repositories\CEmailRepo $emailRepo */
            $emailRepo = \Monkey::app()->repoFactory->create('Email');
            if (!is_array($to)) {
                $to = [$to];

            }
            $emailRepo->newMail($fromEmailAddressId, $to, [], [], $subject, $message);
            $res = "Test Inviato con successo";
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