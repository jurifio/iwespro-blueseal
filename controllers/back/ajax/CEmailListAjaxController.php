<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShipment;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CEmailListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
                      e.id,
                      concat(ea.name, ' ', ea.address) AS `from`,
                      e.subject,
                      group_concat(DISTINCT (CASE WHEN er.typeTo = 'TO' THEN concat(ea2.name, ' ', ea2.address) END )) AS `to`,
                      group_concat(DISTINCT (CASE WHEN er.typeTo = 'CC' THEN concat(ea2.name, ' ', ea2.address) END )) AS `cc`,
                      group_concat(DISTINCT (CASE WHEN er.typeTo = 'BCC' THEN concat(ea2.name, ' ', ea2.address) END )) AS `bcc`,
                      e.submissionDate,
                      max(er.responseDate) lastResponse,
                      AVG(UNIX_TIMESTAMP(er.responseDate)) - UNIX_TIMESTAMP(e.submissionDate) as responseTime,
                      IF (e.isError = 1, 'sisì', 'no') as isError,
                      group_concat(DISTINCT concat(ud.name,' ',ud.surname)) AS userName
                    FROM Email e
                      JOIN EmailAddress ea ON e.fromEmailAddressId = ea.id
                      JOIN EmailRecipient er ON e.id = er.emailId
                      JOIN EmailAddress ea2 ON er.emailAddressId = ea2.id
                      JOIN EmailStatus es ON er.emailStatusId = es.id
                      LEFT JOIN (
                          User u JOIN UserDetails ud ON u.id = ud.userId
                        ) ON ea2.userId = u.id               
                    GROUP BY e.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

      //  $allShop = $this->app->getUser()->hasPermission('allShops');
       // if(!$allShop) {
         //   $datatable->addCondition('scope',[CShipment::SCOPE_SUPPLIER_TO_US]);
        //}

        //$datatable->addCondition('shopId',$this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $val = $this->app->repoFactory->create('Email')->findOne([$row['id']]);

            //$row["DT_RowId"] = $val->printId();
            $row['id'] = $val->printId();
            $row['from'] = $val->from->address;
            $row['subject'] = $val->subject;
            $row['to'] = $val->to;
            $row['cc'] = $val->cc;
            $row['bcc'] = $val->bcc;
            $row['submissionDate'] = STimeToolbox::FormatDateFromDBValue($val->submissionDate, 'Y-m-d');
            $row['lastResponse'] = STimeToolbox::FormatDateFromDBValue($val->lastResponse, 'Y-m-d');
            $row['userName'] = $val->userName;

            $datatable->setResponseDataSetRow($key,$row);

            /*$row['shop'] = $this->app->repoFactory->create('Shop')->findOne([$row['shopId']])->name;
            $row['from'] = $val->carrier->name;
            $row['bookingNumber'] = $val->bookingNumber;
            $row['trackingNumber'] = $val->trackingNumber;
            $row['toAddress'] = $val->toAddress ? ($val->toAddress->subject.'<br />'.$val->toAddress->address.'<br />'.$val->toAddress->city) : '---';
            $row['fromAddress'] = $val->fromAddress ? ($val->fromAddress->subject.'<br />'.$val->fromAddress->address.'<br />'.$val->fromAddress->city) : '---';
            $row['predictedShipmentDate'] = STimeToolbox::FormatDateFromDBValue($val->predictedShipmentDate,'Y-m-d');
            $row['shipmentDate'] = STimeToolbox::FormatDateFromDBValue($val->shipmentDate,'Y-m-d');
            $row['predictedDeliveryDate'] = STimeToolbox::FormatDateFromDBValue($val->predictedDeliveryDate,'Y-m-d');
            $row['deliveryDate'] = STimeToolbox::FormatDateFromDBValue($val->deliveryDate,'Y-m-d');
            $row['cancellationDate'] = ($val->cancellationDate) ? '<span style="color-red">'
                . $val->cancellationDate . '<br />' . $val->shipmentFault->description . '</span>'
                : '';
            $row['creationDate'] = $val->creationDate;
            $row['productContent'] = "";

            $orderlineIds = [];
            foreach ($val->orderLine as $orderLine) {
                if($allShop) $orderlineIds[] = '<a href="/blueseal/ordini/aggiungi?order='.$orderLine->orderId.'">'.$orderLine->printId().'</a>';
                else $orderlineIds[] = $orderLine->printId();
            }
            $row['orderContent'] = implode('<br />',$orderlineIds);
            $row['note'] = $val->note;

            $datatable->setResponseDataSetRow($key,$row);*/
        }

        return $datatable->responseOut();
    }
}