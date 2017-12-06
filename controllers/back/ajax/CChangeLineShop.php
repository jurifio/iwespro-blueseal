<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;

/**
 * Class CChangeLineShop
 * @package bamboo\app\controllers
 */
class CChangeLineShop extends AAjaxController
{
    /**
     * @return bool
     * @throws \bamboo\core\exceptions\RedPandaException
     */
    public function put()
    {
        $datas = $this->data;
        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        /** @var COrderLine $orderLine */
        $orderLine = $orderLineRepo->findOne(['id'=>$datas['orderLineId'],'orderId'=>$datas['orderId']]);
        if (3 > $orderLine->orderLineFriendPaymentStatusId) {
            $orderLine->productSku->stockQty += 1;
            $orderLine->productSku->padding += 1;
            $orderLine->productSku->update();
            $altSku = \Monkey::app()->repoFactory->create('ProductSku')->findOne(['productId' => $orderLine->productId, 'productVariantId' => $orderLine->productVariantId, 'productSizeId' => $orderLine->productSizeId, 'shopId' => $datas['selectShop']]);
            $altSku->stockQty -= 1;
            $altSku->padding -= 1;

            if (!$orderLineRepo->setNewSku($orderLine,$altSku)) {
                $this->app->router->response()->raiseProcessingError();
                return false;
            }
            return true;
        }
        $this->app->router->response()->raiseProcessingError();
        return false;
    }
}