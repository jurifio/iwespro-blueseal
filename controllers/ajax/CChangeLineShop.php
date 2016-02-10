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
class CChangeLineShop extends AAjaxController
{
    /**
     * @return bool
     * @throws \redpanda\core\exceptions\RedPandaException
     */
    public function put()
    {
        $datas = $this->data;
        $orderLine = $this->app->repoFactory->create('OrderLine')->findOne(['id'=>$datas['orderLineId'],'orderId'=>$datas['orderId']]);
        $altSku = $this->app->repoFactory->create('ProductSku')->findOne(['productId'=>$orderLine->productId,'productVariantId'=>$orderLine->productVariantId,'productSizeId'=>$orderLine->productSizeId,'shopId'=>$datas['selectShop']]);
        $olm = new COrderLineManager($this->app,$orderLine);
        if(!$olm->setNewSku($altSku)){
            $this->app->router->response()->raiseProcessingError();
            return false;
        }
        return true;
    }
}