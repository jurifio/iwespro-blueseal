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
class CNewsletterUserManageEdit extends AAjaxController
{
    /**
     * @return int
     * @throws BambooException
     * @throws \Exception
     */
    public function put()
    {
        //prendo i dati passati in input
        $data = \Monkey::app()->router->request()->getRequestData();
        $newsletterId=$data['newsletterId'];
        $name = $data['name'];
        $fromEmailAddressId = $data['fromEmailAddressId'];
        $sendAddressDate = $data['sendAddressDate'];
        $newsletterEmailListId = $data['newsletterEmailListId'];
     //   $newsletterTemplateId = $data['newsletterTemplateId'];
        $subject = $data['subject'];
        $newsletterdataDescription = $data['dataDescription'];
        $preCompiledTemplate = $data['preCompiledTemplate'];
        $campaignId = $data['campaignId'];
        $newsletterEventId = $data['newsletterEventId'];
        $dateCampaignStart = $data['dateCampaignStart'];
        $dateCampaignFinish = $data['dateCampaignFinish'];




        /** @var CRepo $newsletterUserRepo */
        $newsletterUserRepo = \Monkey::app()->repoFactory->create('Newsletter');

        /** @var CNewsletter $newsletter */
        $newsletter = $newsletterUserRepo->findOneBy(['id' => $newsletterId]);



            //se la variabile non è istanziata inserisci in db

            /** @var CNewsletter $newsletter */

            //popolo la tabella

            if (!empty($name)){
            $newsletter->name = $name;
            }
              if (!empty($fromEmailAddressId)){
            $newsletter->fromEmailAddressId = $fromEmailAddressId;
            }
            if (!empty($sendAddressDate)){
                $newsletter->sendAddressDate = $sendAddressDate;
            }
            if (!empty($newsletterEmailListId)){
                $newsletter->newsletterEmailListId = $newsletterEmailListId;
            }
            // if (!empty($newsletterTemplateId)){

                 /** @var CRepo $newsletterTemplateRepo */
              //  $newsletterTemplateRepo = \Monkey::app()->repoFactory->create('NewsletterTemplate');

                 /** @var CNewsletterTemplate $newsletterTemplate */
                // $newsletterTemplate = $newsletterTemplateRepo->findOneBy(['template' => $newsletterTemplateId]);
                 //$newnewsletterTemplateId=$newsletterTemplate->id;

                //$newsletter->newsletterTemplateId = $newnewsletterTemplateId;
         //   }
             if (!empty($subject)){
                $newsletter->subject = $subject;
            }
            if (!empty($newsletterdataDescription)){
                $newsletter->dataDescription = $newsletterdataDescription;
            }
            if (!empty($preCompiledTemplate)){
            $newsletter->preCompiledTemplate = $preCompiledTemplate;
             }
            if (!empty($preCompiledTemplate)){
                $newsletter->preCompiledTemplate = $preCompiledTemplate;
            }
            if (!empty($newsletterCampaignId)){
              $newsletter->newsletterCampaignId = $campaignId;
            }
            if (!empty($newsletterEventId)){
            $newsletter->newsletterEventId = $newsletterEventId;
            }



            // eseguo la commit sulla tabella;

            $newsletter->update();

            $res = "Newsletter modificato con successo!";



        return $res;


    }
    public function secToTime($init){
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;

        return "$hours:$minutes:$seconds";
    }





}