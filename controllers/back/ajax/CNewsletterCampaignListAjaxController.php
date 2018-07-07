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
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterEvent;
use bamboo\domain\entities\CNewsletterUser;

class CNewsletterCampaignListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT
  u.id,
  ud.name as name,
  ud.surname as surname,
  u.email as email
from User u
  INNER  JOIN  UserDetails ud ON u.id = ud.userId
  inner  JOIN  WishList W ON u.id = W.UserId
GROUP BY  u.id
                    ";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        /** @var CRepo $Wlist */
        $nCR = \Monkey::app()->repoFactory->create('NewsletterCampaign');
        $datatable->doAllTheThings(false);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CNewsletterCampaign $nc */
            $nc = $nCR->findOneBy(['id'=>$row['id']]);
            $row['id'] = $nc->id;
            $row['name'] = $nc->name;
            $row['dateCampaignStart'] = $nc->dateCampaignStart;
            $row['dateCampaignFinish'] = $nc->dateCampaignFinish;
            $evs = $nc->newsletterEvent;

            $allEvents = '';
            /** @var CNewsletterEvent $event */
            foreach ($evs as $event){
                $allEvents .= '<strong>ID: </strong>' . $event->id . ' | ' . '<strong>NOME: </strong>' . $event->name . '<br>';
            }

            $row['events'] = $allEvents;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}