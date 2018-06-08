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
class CNewsletterSingleRedemptionListAjaxController extends AAjaxController
{
    public function get()
    {
        $newsletterId = \Monkey::app()->router->request()->getRequestData('newsletterid');

        $sql = "  SELECT 
                        er.emailId as emailId,
                        er.emailAddressId as emailAddressId,
                        er.responseDate as responseDate,
                        er.queuedTime as queuedTime,
                        er.sentTime as sentTime,
                        er.bounceTime as bounceTime,
                        er.firstOpenTime as firstOpenTime,
                        er.firstClickTime as firstClickTime,
                        er.lastClickTime as lastClickTime
                  FROM Newsletter n
                  JOIN Email e ON n.id = e.newsletterId
                  JOIN EmailRecipient er ON e.id = er.emailId
                  WHERE n.id = $newsletterId";

        $datatable = new CDataTables($sql, ['emailAddressId'], $_GET, true);

        $datatable->doAllTheThings('true');


       /* $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $url = $blueseal . "newsletter-redemption/single-redemption?newsletterId=";*/

        foreach ($datatable->getResponseSetData() as $key=>$row) {


        }

        return $datatable->responseOut();
    }
}