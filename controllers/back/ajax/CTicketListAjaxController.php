<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;
use DateTime;

/**
 * Class CBillRegistryProductListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/02/2020
 * @since 1.0
 */
class CTicketListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = 'SELECT
                      t.id as id,
                       DATE_FORMAT(t.dateTicket, "%d-%m-%Y %k:%i:%s") as dateTicket,
                      t.billRegistryClientId as clientId,
       t,shopId as ShopId,
       t.requestTitle as title,
       t.dateCreate as dateCreate,
       t.dateUpdate as dateUpdate,
       t.hour as hour,
       t.cost as cost,
       t.percentageStatus as percentageStatus,
       format((t.cost*t.hour),2) as total,
       `st`.`name` as satus,
       `s`.`name` as shopName,
       `brc`.`companyName` as `companyName`       
                    FROM `Ticket` t join Shop s on t.shopId=s.id 
                    join BillRegistryClient brc on t.billRegistryClientId=brc.id
                    join StatusTicket st on t.statusTicketId=st.id order by t.dateTicket DESC';
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $ticketEdit = $this->app->baseUrl(false) . "/blueseal/ticket/modifica?id=";
        /** @var CTicket $ticketRepo */
        $ticketRepo = \Monkey::app()->repoFactory->create('Ticket');
        /** @var  CShop $shopRepo */
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        /**
         * @var  CBillRegistryClient $brcRepo;
         */
        $brcRepo=\Monkey::app()->repoFactory->create('BillRegistryClient');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CTicket $ticket */
            $ticket = $ticketRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] =  $ticket->printId();
            $row['id'] = '<a href="'.$ticketEdit.$ticket->id.'">'.$ticket->id.'</a>';
            $row['requestTitle'] = $ticket->codeProduct;

            $row['dateTicket'] =(new \DateTime($ticket->dateTicket))->format('d-m-Y H:i:s');
            $shop=$shopRepo->findOneBy(['id'=>$ticket->shopId]);
            $row['shopName']=$shop->name;
            $brc=$brcRepo->findOneBy(['id'=>$ticket->billRegistrryClientId]);
            $row['client'] = $brc->companyName;
            $row['cost'] = (!is_null($ticket->cost)) ? number_format($ticket->cost,2,',', '.'):'0';
            $row['hour'] = (!is_null($ticket->hour))?  number_format($ticket->hour,2,',', '.'):'0';
            $row['total'] =(!is_null($ticket->hour))?  number_format(($ticket->cost*$ticket->hour),2,',','.'):'0';
            $row['percentageStatus']=$ticket->percentageStatus;
            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}