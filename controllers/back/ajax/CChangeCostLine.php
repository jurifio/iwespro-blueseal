<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\controllers\back\ajax;
use bamboo\domain\repositories\COrderLineRepo;

/**
 * Class CChangeLineShop
 * @package bamboo\app\controllers
 */
class CChangeCostLine extends AAjaxController
{
    /**
     * @return bool
     */
    public function put()
    {
        $datas = $this->data;
        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $orderLine = $orderLineRepo->findOne(['id'=>$datas['orderLineId'],'orderId'=>$datas['orderId']]);
        $orderLineRepo->changeCost($orderLine,$datas['change_cost']);
        return true;
    }

}