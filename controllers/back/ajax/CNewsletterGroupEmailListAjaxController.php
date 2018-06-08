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
class CNewsletterGroupEmailListAjaxController extends AAjaxController
{
    public function get()
    {
        $id = \Monkey::app()->router->request()->getRequestData('id');
        /** @var CRepo $newsletterGroupRepo */
//$id=1;
        $newsletterGroupRepo = \Monkey::app()->repoFactory->create('NewsletterGroup');

        /** @var CNewsletterGroup $newsletterGroup */

        $newsletterGroup = $newsletterGroupRepo->findOneBy(['id' => $id]);
         $sql=$newsletterGroup->sql;
         $sql=$sql ." group by nu.email";



        $datatable = new CDataTables($sql, ['email'], $_GET, true);

        $datatable->doAllTheThings('true');


       /* $blueseal = $this->app->baseUrl(false) . '/blueseal/';
        $url = $blueseal . "newsletter-redemption/single-redemption?newsletterId=";*/

        foreach ($datatable->getResponseSetData() as $key=>$row) {


        }

        return $datatable->responseOut();
    }
}