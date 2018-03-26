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
        $sql = "SELECT 
                 n.id, 
                 n.name,
                 E.address as fromEmailAddressId, 
                 n.sendAddressDate,  
                 L.name as newsletterEmailListId, 
                 T.name as templateName, 
                 n.subject, 
                 C.name as campaignId  
                FROM Newsletter n 
                inner join   EmailAddress E ON n.fromEmailAddressId = E.id 
                inner join NewsletterEmailList L ON n.newsletterEmailListId = L.id 
                inner join NewsletterCampaign C ON n.newsletterCampaignId = C.id 
                INNER join NewsletterTemplate T ON n.newsletterTemplateId = T.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);



        /** @var CNewsletterRepo $newsletterRepo */
        $newsletterRepo = \Monkey::app()->repoFactory->create('Newsletter');


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $opera = $blueseal . "newsletter/modifica?newsletter=";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CNewsletter $newsletter */
            $newsletter = $newsletterRepo->findOneBy(['id' => $row["id"] ]);

            $row['id'] = '<a href="' . $opera . $newsletter->id . '" >' . $newsletter->id . '</a>';


            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();

    }
}