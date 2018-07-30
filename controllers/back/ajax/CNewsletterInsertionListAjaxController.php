<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CNewsletterInsertion;
use bamboo\domain\repositories\CNewsletterInsertionRepo;

class CNewsletterInsertionListAjaxController extends AAjaxController
{

    public function get()
    {

        $eventId = \Monkey::app()->router->request()->getRequestData('eventid');

        $sql = "SELECT 
            ni.id,
            ni.name as insertionName,
            ne.name as eventName,
            ne.id as newsletterEId,
            nc.name as newsletterCampaignName
        FROM NewsletterInsertion ni
        JOIN NewsletterEvent ne ON ne.id = ni.newsletterEventId
        JOIN NewsletterCampaign nc ON ne.newsletterCampaignId = nc.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        if($eventId !== ":eventId") $datatable->addCondition('newsletterEId', [$eventId]);

        $datatable->doAllTheThings(false);

        /** @var CNewsletterInsertionRepo $insertionRepo */
        $insertionRepo = \Monkey::app()->repoFactory->create('NewsletterInsertion');

        $url = $this->app->baseUrl(false).'/blueseal/newsletter-user/';
        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CNewsletterInsertion $ins */
            $ins = $insertionRepo->findOneBy(['id' => $row["id"]]);
            $row['row_id'] = $ins->id;
            $row['id'] = "<a href='". $url.$ins->id . "' target='_blank'>".$ins->id."</a>";
            $row['insertionName'] = $ins->name;
            $row['eventName'] = $ins->newsletterEvent->name;
            $row['newsletterCampaignName'] = $ins->newsletterEvent->newsletterCampaign->name;

            $datatable->setResponseDataSetRow($key,$row);

        }

        return $datatable->responseOut();
    }
}