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
use bamboo\domain\entities\CNewsletterUser;

class CNewsletterUserListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT n.id, n.name, E.address as fromEmailAddressId, n.sendAddressDate,  L.name as newsletterEmailListId, Template.template  , n.subject, C.name as campaignId  FROM
  NewsletterUser n inner join   EmailAddress E ON n.fromEmailAddressId = E.id inner join NewsletterEmailList L ON n.newsletterEmailListId = L.id inner join Campaign C ON n.campaignId = C.id INNER join NewsletterTemplate Template ON n.newsletterTemplateId = Template.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {





        }

        return $datatable->responseOut();
    }
}