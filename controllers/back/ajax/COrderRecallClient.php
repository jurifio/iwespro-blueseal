<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\repositories\CEmailRepo;

/**
 * Class COrderRecallClient
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class COrderRecallClient extends AAjaxController
{
    use TMySQLTimestamp;

    public function post()
    {
        $langId = $this->app->router->request()->getRequestData('langId');
        $lang = \Monkey::app()->repoFactory->create('Lang')->findOneByStringId($langId);

	    foreach ($this->app->router->request()->getRequestData('ordersId') as $orderId) {
            $order = \Monkey::app()->repoFactory->create('Order')->findOneByStringId($orderId);

            $to = [$order->user->email];

            /** @var CEmailRepo $emailRepo */
            $emailRepo = \Monkey::app()->repoFactory->create('Email');
            $res = $emailRepo->newPackagedMail('remindmailclient','no-reply@pickyshop.com', $to,[],[],['order'=>$order,'orderId'=>$orderId,'lang'=>$lang->lang],'MailGun',null);

            //$this->app->mailer->newPackagedMail('remindmailclient','no-reply', $to,[],[],['order'=>$order,'orderId'=>$orderId,'lang'=>$lang->lang]);

            /*if($this->app->mailer->send()) {
                $order->note = $order->note." RemindMail: ".date('Y-m-d');
                $order->update();
            };*/

            if($res) {
                $order->note = $order->note." RemindMail: ".date('Y-m-d');
                $order->update();
            };
        }
    }
}