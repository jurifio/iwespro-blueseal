<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\email\CEmail;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CEmailRecipient;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\CEmailRecipientRepo;
use bamboo\domain\repositories\CEmailRepo;
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

    public function secToTime($init){
        $hours = floor($init / 3600);
        $minutes = floor(($init / 60) % 60);
        $seconds = $init % 60;

        return "$hours:$minutes:$seconds";
    }

    public function get()
    {
        $sql = "SELECT
                      e.id,
                      concat(ea.name, ' ', ea.address) AS `from`,
                      e.subject,
                      group_concat(DISTINCT (CASE WHEN er.typeTo = 'TO' THEN concat(ea2.name, ' ', ea2.address) END )) AS `to`,
                      group_concat(DISTINCT (CASE WHEN er.typeTo = 'CC' THEN concat(ea2.name, ' ', ea2.address) END )) AS `cc`,
                      group_concat(DISTINCT (CASE WHEN er.typeTo = 'BCC' THEN concat(ea2.name, ' ', ea2.address) END )) AS `bcc`,
                      e.htmlBody,
                      e.submissionDate,
                      max(er.responseDate) as lastResponse,
                      AVG(UNIX_TIMESTAMP(er.responseDate)) - UNIX_TIMESTAMP(e.submissionDate) as responseTime,
                      IF (e.isError = 1, 'sisì', 'no') as isError,
                      group_concat(DISTINCT concat(ud.name,' ',ud.surname)) AS userName
                    FROM Email 
                    e
                      JOIN EmailAddress ea ON e.fromEmailAddressId = ea.id
                      JOIN EmailRecipient er ON e.id = er.emailId
                      JOIN EmailAddress ea2 ON er.emailAddressId = ea2.id
                      JOIN EmailStatus es ON er.emailStatusId = es.id
                      LEFT JOIN (
                          User u JOIN UserDetails ud ON u.id = ud.userId
                        ) ON ea2.userId = u.id               
                    GROUP BY e.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);


        $emailRepo = \Monkey::app()->repoFactory->create('Email');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var \bamboo\domain\entities\CEmail $email */
            $email = $emailRepo->findOneBy($row);
            //$email = $emailRepo->findOneByStringId($email->printId());
            $row["DT_RowId"] = $email->printId();

            $error = $email->isError;
            if ($error){
                $row["DT_RowClass"] = "red";
            } else {
                $isAccepted = true;
                $isDelivered = true;
                $isPartialDelivered = false;
                $isPartialDropped = false;
                $isDropped = true;
                foreach ($email->emailRecipient as $emailRecipient){
                    /** @var CEmailRecipient $emailRecipient */
                    if (!$emailRecipient->isAccepted()) {
                        $isAccepted = false;
                        break;
                    }
                    if ($emailRecipient->isDelivered()) {
                        $isPartialDelivered = true;
                    } else {
                        $isDelivered = false;
                    }

                    if($emailRecipient->isError()) {
                        $isPartialDropped = true;
                    } else {
                        $isDropped = false;
                    }
                }

                if($isDropped) {
                    $row["DT_RowClass"] = "red";
                    //rosso
                } elseif($isPartialDropped) {
                } elseif ($isDelivered) {
                    $row["DT_RowClass"] = "green";
                    //verde
                } elseif ($isPartialDelivered) {
                    $row["DT_RowClass"] = "yellow";
                    //giallo
                } elseif($isAccepted) {
                    $row["DT_RowClass"] = "grey";
                    //grigio
                } else {
                    $row["DT_RowClass"] = "violet";
                    //rosso
                }
            }

            $row['from'] = $email->fromEmailAddress->getPrettyEmailAddress();
            $row['to'] = str_replace(',','<br />',$row['to']);
            $row['cc'] = str_replace(',','<br />',$row['cc']);
            $row['bcc'] = str_replace(',','<br />',$row['bcc']);
            $row['htmlBody'] = substr(trim(strip_tags($row['htmlBody'])), 0,50)."...";
            $row['responseTime'] = $this->secToTime($row['responseTime']);


        $datatable->setResponseDataSetRow($key,$row);

        }

        return $datatable->responseOut();
    }
}