<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CNewsletterRedemptionListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/02/2018
 * @since 1.0
 */
class CNewsletterRedemptionGroupedListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "  SELECT
                  e.id                                                                     AS emailId,
                  n.id                                                                     AS newsletterId,
                  n.newsletterCloneId                                                      AS newsletterCloneId,
                  n.name                                                                   AS newsletterName,
                  count(DISTINCT er.emailAddressId)                                        AS emailAddressCount,
                  count(distinct er.emailStatusId=1)                                       AS emailPending,
                  count(distinct er.emailStatusId=2)                                       AS emailNotQueued,
                  count(distinct er.emailStatusId=3)                                       AS emailAccepted,
                  count(distinct er.emailStatusId=4)                                       AS emailDelivered,
                  count(distinct er.emailStatusId=5)                                       AS emailDropped,
                  round(AVG(TIMESTAMPDIFF(SECOND, er.queuedTime, er.sentTime)),0)          AS sendingTime,
                  round(AVG(TIMESTAMPDIFF(SECOND, er.sentTime, er.firstOpenTime)),0)       AS openTimeSinceSent,
                  round(AVG(TIMESTAMPDIFF(SECOND, er.firstOpenTime, er.firstClickTime)),0) AS clickTimeSinceOpened,
                  round(AVG(TIMESTAMPDIFF(SECOND, er.sentTime, er.lastClickTime)),0)       AS aliveTime,
                  round(count(er.sentTime) * 100 / count(e.id),2)                          AS sentPercent,
                  round(count(er.firstOpenTime) * 100 / count(er.sentTime),0)              AS openedPercent,
                  round(count(er.firstClickTime) * 100 / count(er.sentTime),0)             AS clickedPercent,
                  
                  n.id                                                            
                
                
                FROM Newsletter n
                  JOIN Email e ON n.id = e.newsletterId
                  JOIN EmailRecipient er ON e.id = er.emailId
                  group  BY n.newsletterCloneId ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');


        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $url = $blueseal . "newsletter-redemption/single-redemption?newsletterId=";

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            $row["newsletterId"] = '<a href="'. $url.$row["newsletterId"] . '" target="_blank">' . $row["newsletterId"] . '</a>';
            $row["newsletterCloneId"]="Newsletter :".$row['newsletterCloneId'];

            $row["sendingTime"] = $row["sendingTime"].'s';
            $row["openTimeSinceSent"] = $row["openTimeSinceSent"].'s';
            $row["clickTimeSinceOpened"] = $row["clickTimeSinceOpened"].'s';
            $row["aliveTime"] = $row["aliveTime"].'s';

            $row["sentPercent"] = $row["sentPercent"].'%';
            $row["openedPercent"] = $row["openedPercent"].'%';
            $row["clickedPercent"] = $row["clickedPercent"].'%';

            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}