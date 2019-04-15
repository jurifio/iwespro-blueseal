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
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\CNewsletterUser;
use bamboo\domain\repositories\CNewsletterRepo;

class CNewsletterUserListAjaxController extends AAjaxController
{

    public function get()
    {

        $insertionId = \Monkey::app()->router->request()->getRequestData('insertionid');

        $sql = "SELECT 
                 n.id, 
                 n.newsletterCloneId AS newsletterCloneId,
                 n.name,
                 ea.address AS fromEmailAddressId, 
                 n.sendAddressDate,  
                 e.submissionDate AS submissionDate,
                 nel.name AS newsletterEmailListId, 
                 t.name AS templateName, 
                 n.subject, 
                 concat(nc.id, '-', nc.name) AS campaignId,
                 ni.name as newsletterInsertionName,
                 ne.name as eventName,
                 ni.id as newsletterInsertionId
                FROM Newsletter n 
                JOIN NewsletterInsertion ni ON n.newsletterInsertionId = ni.id
                JOIN NewsletterEvent ne ON ni.newsletterEventId = ne.id
                JOIN NewsletterCampaign nc ON nc.id = ne.newsletterCampaignId
                INNER JOIN EmailAddress ea ON n.fromEmailAddressId = ea.id 
                LEFT OUTER JOIN Email e ON n.id=e.newsletterId
                INNER JOIN NewsletterEmailList nel ON n.newsletterEmailListId = nel.id 
                INNER JOIN NewsletterTemplate t ON n.newsletterTemplateId = t.id
                ORDER BY newsletterCloneId ASC ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        if($insertionId !== ":insertionId") $datatable->addCondition('newsletterInsertionId', [$insertionId]);

        $datatable->doAllTheThings(true);


        /** @var CNewsletterRepo $newsletterRepo */
        $newsletterRepo = \Monkey::app()->repoFactory->create('Newsletter');


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "newsletter/modifica?newsletter=";

        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CNewsletter $newsletter */
            $newsletter = $newsletterRepo->findOneBy(['id' => $row["id"]]);
            if ($row['submissionDate'] == "") {
                $row['id'] = '<a href="' . $opera . $newsletter->id . '">' . $newsletter->id . '</a>';
                $row['name'] = $newsletter->name;
                $row['sendAddressDate'] = $newsletter->sendAddressDate;
                $row['fromEmailAddressId'] = $newsletter->emailAddress->address;
                $row['newsletterEmailListId'] = $newsletter->newsletterEmailList->name;
                $row['templateName'] = $newsletter->newsletterTemplate->name;
                $row['subject'] = $newsletter->subject;
                $row['newsletterInsertionName'] = $newsletter->newsletterInsertion->name;
                $row['eventName'] = $newsletter->newsletterInsertion->newsletterEvent->name;
                $row['campaignId'] = $newsletter->newsletterInsertion->newsletterEvent->newsletterCampaign->id . '-' . $newsletter->newsletterInsertion->newsletterEvent->newsletterCampaign->name;

                if ($newsletter->id == $newsletter->newsletterCloneId) {
                    $row['newsletterCloneId'] = "Newsletter Genitore";
                } else {
                    $row['newsletterCloneId'] = "Newsletter figlia di :" . $newsletter->newsletterCloneId;
                }
            }
            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();

    }
}