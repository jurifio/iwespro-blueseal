<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\blueseal\controllers\ajax;
use redpanda\blueseal\business\COrderLineManager;

/**
 * Class CChangeLineShop
 * @package redpanda\app\controllers
 */
class CChangeFriendRevenue extends AAjaxController
{
    /**
     * @return bool
     */
    public function put()
    {
        $datas = $this->data;
        $orderLine = $this->app->repoFactory->create('OrderLine')->findOne(['id'=>$datas['orderLineId'],'orderId'=>$datas['orderId']]);
        $olm = new COrderLineManager($this->app,$orderLine);
        $olm->changeFriendRevenue($datas['change_revenue']);
        return true;
    }

}