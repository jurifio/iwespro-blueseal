<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterInsertion;
use bamboo\domain\entities\CNewsletterShop;

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
        $campaignId = \Monkey::app()->router->request()->getRequestData('campaignId');
        $newsletterShopId = \Monkey::app()->router->request()->getRequestData('newsletterShopId');
        $res = null;

        if($insertionId) {
            /** @var CNewsletterInsertion $ins */
            $ins = \Monkey::app()->repoFactory->create('NewsletterInsertion')->findOneBy(["id" => $insertionId]);

            $res['campaignId'] = $ins->newsletterEvent->newsletterCampaign->id;
            $res['campaignName'] = $ins->newsletterEvent->newsletterCampaign->name;
            $res['eventId'] = $ins->newsletterEvent->id;
            $res['eventName'] = $ins->newsletterEvent->name;
            $res['insertionName'] = $ins->name;
            $res['emailId'] = $ins->newsletterEvent->newsletterCampaign->newsletterShop->fromEmailAddressId;
            $res['email'] = $ins->newsletterEvent->newsletterCampaign->newsletterShop->emailAddress->address;
        }

        if($campaignId) {
            /** @var CNewsletterCampaign $cam */
            $cam = \Monkey::app()->repoFactory->create('NewsletterCampaign')->findOneBy(["id" => $campaignId]);
            $res['emailId'] = $cam->newsletterShop->fromEmailAddressId;
            $res['email'] = $cam->newsletterShop->emailAddress->address;
        }

        if($newsletterShopId) {
            /** @var CNewsletterShop $shop */
            $shop = \Monkey::app()->repoFactory->create('NewsletterShop')->findOneBy(["id" => $newsletterShopId]);
            $res['emailId'] = $shop->fromEmailAddressId;
            $res['email'] = $shop->emailAddress->address;
        }

        return json_encode($res);

    }
}