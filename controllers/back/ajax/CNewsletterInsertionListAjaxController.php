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
            ne.name as eventName
        FROM NewsletterInsertion ni
        JOIN NewsletterEvent ne ON ne.id = ni.newsletterEventId
        WHERE newsletterEventId = $eventId";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(false);

        /** @var CNewsletterInsertionRepo $insertionRepo */
        $insertionRepo = \Monkey::app()->repoFactory->create('NewsletterInsertion');


        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CNewsletterInsertion $ins */
            $ins = $insertionRepo->findOneBy(['id' => $row["id"]]);
            $row['row_id'] = $ins->id;
            $row['id'] = $ins->id;
            $row['insertionName'] = $ins->name;
            $row['eventName'] = $ins->newsletterEvent->name;

            $datatable->setResponseDataSetRow($key,$row);

        }

        return $datatable->responseOut();
    }
}