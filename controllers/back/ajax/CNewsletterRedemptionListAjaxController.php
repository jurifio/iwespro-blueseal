<?php

namespace bamboo\controllers\back\ajax;

use Aws\CloudFormation\Enum\StackStatus;
use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\utils\time\STimeToolbox;


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
class CNewsletterRedemptionListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "  SELECT
                  e.id                                                                     AS emailId,
                  n.id                                                                     AS newsletterId,
                  n.newsletterCloneId                                                      AS newsletterCloneId,
                  n.name                                                                   AS newsletterName,
                  count(DISTINCT er.emailAddressId)                                        AS emailAddressCount,
                  count(CASE er.emailStatusId WHEN 1 THEN 1 ELSE NULL END)                 AS emailPending,
                  count(CASE er.emailStatusId WHEN 2 THEN 1 ELSE NULL END)                 AS emailNotQueued,
                  count(CASE er.emailStatusId WHEN 3 THEN 1 ELSE NULL END)                 AS emailAccepted,
                  count(CASE er.emailStatusId WHEN 4 THEN 1 ELSE NULL END)                 AS emailDelivered,
                  count(CASE er.emailStatusId WHEN 5 THEN 1 ELSE NULL END)                 AS emailDropped,
                  count(CASE er.emailStatusId WHEN 6 THEN 1 ELSE NULL END)                 AS emailOpened,
                  count(CASE er.emailStatusId WHEN 7 THEN 1 ELSE NULL END)                 AS emailClicked,
                  #round(AVG(TIMESTAMPDIFF(SECOND, er.queuedTime, er.sentTime)),0)          AS sendingTime,
                  round(AVG(TIMESTAMPDIFF(SECOND, er.sentTime, er.firstOpenTime)),0)       AS openTimeSinceSent,
                  round(AVG(TIMESTAMPDIFF(SECOND, er.firstOpenTime, er.firstClickTime)),0) AS clickTimeSinceOpened,
                  round(AVG(TIMESTAMPDIFF(SECOND, er.sentTime, er.lastOpenTime)),0)       AS aliveTime,
                  round(count(er.sentTime) * 100 / count(e.id),2)                          AS sentPercent,
                  round(count(er.firstOpenTime) * 100 / count(er.sentTime),0)              AS openedPercent,
                  round(count(er.firstClickTime) * 100 / count(er.sentTime),0)             AS clickedPercent,
                  n.id,
                  ni.name as insertionName,
                  ne.name as eventName,
                  nc.name as campaignName
                FROM Newsletter n
                  JOIN NewsletterInsertion ni ON n.newsletterInsertionId = ni.id
                  JOIN NewsletterEvent ne ON ni.newsletterEventId = ne.id
                  JOIN NewsletterCampaign nc ON ne.newsletterCampaignId = nc.id
                  JOIN Email e ON n.id = e.newsletterId
                  JOIN EmailRecipient er ON e.id = er.emailId
                  GROUP  BY n.id ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');

        $sTime = new STimeToolbox();

        $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $url = $blueseal . "newsletter-redemption/single-redemption?newsletterId=";

        foreach ($datatable->getResponseSetData() as $key => $row) {

            $row["newsletterId"] = '<a href="' . $url . $row["newsletterId"] . '" target="_blank">' . $row["newsletterId"] . '</a>';
            $row["newsletterCloneId"] = "Newsletter :" . $row['newsletterCloneId'];

            $row["openTimeSinceSent"] = $sTime->secondsToTime($row["openTimeSinceSent"], true);
            $row["clickTimeSinceOpened"] = $sTime->secondsToTime($row["clickTimeSinceOpened"], true);
            $row["aliveTime"] = $sTime->secondsToTime($row["aliveTime"], true);

            $row["sentPercent"] = $row["sentPercent"] . '%';
            $row["openedPercent"] = $row["openedPercent"] . '%';
            $row["clickedPercent"] = $row["clickedPercent"] . '%';

            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}