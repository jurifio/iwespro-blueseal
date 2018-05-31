<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\repositories\CNewsletterUserRepo;


/**
 * Class CNewsletterClone
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
class CNewsletterClone extends AAjaxController
{



    /**
     * @return mixed
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */

    public function put(){
        $data  = $this->app->router->request()->getRequestData();
        $id = $data["id"];
        $value = $id;
        $value = strstr($value, ">");
        $value = strstr($value, "</a>", true);
        $value =trim(str_replace(">","",$value));
        /** @var CRepo $newsletterUserRepo */
        $newsletterUserRepo = \Monkey::app()->repoFactory->create('Newsletter');

        /** @var CNewsletter $newsletter */
        $newsletter = $newsletterUserRepo->findOneBy(['id'=>$value]);
        $newsletterCloneName=$newsletter->name;
        $newsletterCloneFromEmailAddressId=$newsletter->fromEmailAddressId;
        $newsletterCloneSendAddressdate='2100-00-00 00:00:00';
        $newsletterCloneNewsletterEmailListId=$newsletter->newsletterEmailListId;
        $newsletterCloneNewsletterTemplateId=$newsletter->newsletterTemplateId;
        $newsletterCloneSubject=$newsletter->subject;
        $newsletterCloneDataDescription=$newsletter->dataDescription;
        $newsletterClonePreCompiledTemplate=$newsletter->preCompiledTemplate;
        $newsletterCloneNewsletterCampaignId=$newsletter->newsletterCampaignId;
        $newsletterCloneCampaignId=$newsletter->campaignId;
        $newsletterCloneEventId=$newsletter->newsletterEventId;
        $newsletterCloneName="Copia di ".$newsletterCloneName;
        /** @var CNewsletterUser $newsletterUserInsert */
        $newsletterUserInsert = \Monkey::app()->repoFactory->create('Newsletter')->getEmptyEntity();
        //popolo la tabella

        $newsletterUserInsert->name = $newsletterCloneName;
        $newsletterUserInsert->fromEmailAddressId = $newsletterCloneFromEmailAddressId;
        $newsletterUserInsert->sendAddressDate = $newsletterCloneSendAddressdate;
        $newsletterUserInsert->newsletterEmailListId = $newsletterCloneNewsletterEmailListId;
        $newsletterUserInsert->newsletterTemplateId = $newsletterCloneNewsletterTemplateId;
        $newsletterUserInsert->subject = $newsletterCloneSubject;
        $newsletterUserInsert->dataDescription = $newsletterCloneDataDescription;
        $newsletterUserInsert->preCompiledTemplate = $newsletterClonePreCompiledTemplate;
        $newsletterUserInsert->newsletterCampaignId = $newsletterCloneNewsletterCampaignId;
        $newsletterUserInsert->CampaignId = $newsletterCloneCampaignId;
        $newsletterUserInsert->newsletterEventId = $newsletterCloneEventId;
        $newsletterUserInsert->newsletterCloneId=$value;
        // eseguo la commit sulla tabella;

        $newsletterUserInsert->smartInsert();




        $res = " Newsletter Duplicata";
        return $res;

    }



}