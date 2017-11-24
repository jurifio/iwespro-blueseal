<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\traits\TMySQLTimestamp;
use bamboo\domain\entities\CShipment;
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
class COrderWireTransferMailClient extends AAjaxController
{
    use TMySQLTimestamp;

    public function post()
    {
        $langId = $this->app->router->request()->getRequestData('langId');
        $lang = $this->app->repoFactory->create('Lang')->findOneByStringId($langId);

	    foreach ($this->app->router->request()->getRequestData('ordersId') as $orderId) {
            $order = $this->app->repoFactory->create('Order')->findOneByStringId($orderId);

            $to = [$order->user->email];
            //$this->app->mailer->prepare('wiretransfermailclient','no-reply', $to,[],[],['order'=>$order,'orderId'=>$orderId,'lang'=>$lang->lang]);

            /** @var CEmailRepo $mailRepo */
            $mailRepo = \Monkey::app()->repoFactory->create('Email');
            $res = $mailRepo->newPackagedMail('wiretransfermailclient','no-reply@pickyshop.com', $to,[],[],['order'=>$order,'orderId'=>$orderId,'lang'=>$lang->lang]);

           /* if($this->app->mailer->send()) {
                $order->note = $order->note." BonificoMail: ".date('Y-m-d');
                $order->update();
            };*/

            if($res) {
                $order->note = $order->note." BonificoMail: ".date('Y-m-d');
                $order->update();
            };
        }
    }
}