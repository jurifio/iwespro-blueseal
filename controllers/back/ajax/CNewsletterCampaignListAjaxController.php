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
        $sql = "SELECT n.id as linkId, 
                        n.name,  
                        n.dateCampaignStart, 
                        n.dateCampaignFinish,
                        group_concat(concat(ne.id,' | ',ne.name)) as events,
                        ns.name as newsletterShop
                    from NewsletterCampaign n
                    LEFT JOIN NewsletterEvent ne ON n.id = ne.newsletterCampaignId
                    LEFT JOIN NewsletterShop ns ON ns.id = n.newsletterShopId
                    GROUP BY n.id
                    ";
        $datatable = new CDataTables($sql, ['linkId'], $_GET, true);

        /** @var CRepo $nCR */
        $nCR = \Monkey::app()->repoFactory->create('NewsletterCampaign');
        $datatable->doAllTheThings(false);
        $url = $this->app->baseUrl(false).'/blueseal/newsletter-lista-eventi/';

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CNewsletterCampaign $nc */
            $nc = $nCR->findOneBy(['id'=>$row['linkId']]);
            $row['id'] = $nc->id;
            $row['linkId'] = "<a href='". $url.$nc->id . "' target='_blank'>".$nc->id."</a>";
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

            $row["newsletterShop"] = is_null($nc->newsletterShop) ? '---' : $nc->newsletterShop->name;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}