<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterUser;

class CNewsletterEventListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT 
                      n.id as linkId, 
                      n.name as eventName , 
                      N2.name as campaignName,  
                      n.emailSent, 
                      n.emailDelivered, 
                      n.emailOpened, 
                      n.emailClicked 
                FROM NewsletterEvent n
                JOIN NewsletterCampaign N2 ON n.newsletterCampaignId = N2.id";

        $datatable = new CDataTables($sql, ['linkId'], $_GET, true);

        $datatable->doAllTheThings(false);


        /** @var CRepo $nERepo */
        $nERepo = \Monkey::app()->repoFactory->create('NewsletterEvent');
        $url = $this->app->baseUrl(false).'/blueseal/newsletter-lista-inserzioni/';
        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CNewsletterEvent $event */
            $event = $nERepo->findOneBy(['id'=>$row['linkId']]);
            $row['id'] = $event->id;
            $row['linkId'] = "<a href='". $url.$event->id . "' target='_blank'>".$event->id."</a>";
            $row['eventName'] = $event->name;
            $row['campaignName'] = $event->newsletterCampaign->name;
            $row['emailSent'] = $event->emailSent;
            $row['emailDelivered'] = $event->emailDelivered;
            $row['emailOpened'] = $event->emailOpened;
            $row['emailClicked'] = $event->emailClicked;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}