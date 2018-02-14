<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CProductDetailListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNewsletterRedemptionListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = "SELECT
n.name,
count(DISTINCT er.emailAddressId),
avg(er.sentTime - er.queuedTime) AS sendingTime,
avg(er.sentTime - er.queuedTime) AS deliveryTime,
avg(er.firstOpenTime - er.sentTime) AS openTimeSinceDelivered,
avg(er.firstClickTime - er.firstOpenTime) AS clickTimeSinceOpened,
avg(er.lastClickTime - er.sentTime) AS aliveTime,
round(count(e.id) / count(er.sentTime) * 100, 2) as sentPercent,
round(count(e.id) / count(deliveryTime) * 100, 2) as deliveredPercent,
round(count(deliveredPercent) / count(er.firstOpenTime) * 100, 2) as openedPercent,
round(count(deliveredPercent) / count(er.firstClickTime) * 100, 2) as clickedPercent
FROM Newsletter n
JOIN Email e ON n.id = e.newsletterId
JOIN EmailRecipient er ON e.id = er.emailId
GROUP BY n.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

        }

        return $datatable->responseOut();
    }
}