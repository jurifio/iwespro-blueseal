<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterInsertion;

/**
 * Class CNewsletterShopManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 17/07/2018
 * @since 1.0
 */
class CNewsletterShopManage extends AAjaxController
{
    public function get()
    {
        $insertionId = \Monkey::app()->router->request()->getRequestData('insertionId');


        /** @var CNewsletterInsertion $ins */
        $ins = \Monkey::app()->repoFactory->create('newsletterInsertion')->findOneBy(["id"=>$insertionId]);

        $res['campaignId'] = $ins->newsletterEvent->newsletterCampaign->id;
        $res['eventId'] = $ins->newsletterEvent->id;

        return json_encode($res);

    }
}