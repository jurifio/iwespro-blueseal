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

class CNewsletterCampaignListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT n.id, n.name,  n.dateCampaignStart, n.dateCampaignFinish from NewsletterCampaign n";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {




        }

        return $datatable->responseOut();
    }
}