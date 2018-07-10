<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\repositories\CNewsletterEventRepo;


/**
 * Class CNewsletterEventManage
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
class CNewsletterEventManage extends AAjaxController
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

        $nameEvent = $data["nameEvent"];
        if(empty($nameEvent)) return false;

        if($data["type"] == 1){
            $campaignId = $data['campaignId'];
        } else {
            if(empty($data["nameCampaign"])) return false;
            /** @var CRepo $nCRepo */
            $nCRepo = \Monkey::app()->repoFactory->create('NewsletterCampaign');

            /** @var CNewsletterCampaign $nC */
            $nC = $nCRepo->getEmptyEntity();
            $nC->name = $data['nameCampaign'];
            $nC->dateCampaignStart = $data['startDate'];
            $nC->dateCampaignFinish = $data['endDate'];
            $nC->smartInsert();

            $campaignId = $nC->id;
        }


        /** @var CRepo $newsletterEventRepo */
        $newsletterEventRepo = \Monkey::app()->repoFactory->create('NewsletterEvent');

        /** @var CNewsletterEvent $newsletterEventInsert   */
        $newsletterEventInsert = $newsletterEventRepo->getEmptyEntity();
        $newsletterEventInsert->name = $nameEvent ;
        $newsletterEventInsert->newsletterCampaignId = $campaignId;
        $newsletterEventInsert->smartInsert();

        return $campaignId;

    }

        public function put()
        {
            $data  = $this->app->router->request()->getRequestData();
            $id = $data["id"];
            $name = $data["name"];
            $campaignId = $data["campaignId"];


            /** @var CRepo $newsletterEvent */
            $newsletterEvent = \Monkey::app()->repoFactory->create('NewsletterEvent');

            /** @var CNewsletterEvent $newsletter */
            $newsletter = $newsletterEvent->findOneBy(['id'=>$id]);

            $newsletter->name = $name;
            $newsletter->newsletterCampaignId = $campaignId;



            $newsletter->update();

            $res = "Evento Campagna aggiornato";
            return $res;



        }

}