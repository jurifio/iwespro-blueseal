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

class CNewsletterEventListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT n.id, n.name as eventName , N2.name as campaignName,  n.emailSent, n.emailDelivered, n.emailOpened, n.emailClicked from NewsletterEvent n
        inner join NewsletterCampaign N2 ON n.newsletterCampaignId = N2.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

        }

        return $datatable->responseOut();
    }
}