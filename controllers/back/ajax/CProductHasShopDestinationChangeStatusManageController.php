<?php


namespace bamboo\controllers\back\ajax;


use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CShopHasProduct;
use bamboo\domain\entities\CProductHasShopDestination;


/**
 * Class CProductHasShopDestinationChangeStatusManageController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 26/08/2019
 * @since 1.0
 */
class CProductHasShopDestinationChangeStatusManageController extends AAjaxController
{
    public function get()
    {
        $response = [];
        foreach (\Monkey::app()->repoFactory->create('Shop')->findAll() as $shop) {
            if($shop->hasEcommerce==1) {
                $response[] = ['id' => $shop->printId(), 'name' => $shop->name, 'title' => $shop->title];
            }
        }

        return json_encode($response);
    }

    public function post()
    {

        $shopIdDestination = $this->app->router->request()->getRequestData('shop');
        $rows = $this->app->router->request()->getRequestData('rows');
        $productHasShopDestinationRepo = \Monkey::app()->repoFactory->create('ProductHasShopDestination');
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        $status=$this->app->router->request()->getRequestData('status');





        \Monkey::app()->repoFactory->beginTransaction();
        $i = 0;
        foreach ($rows as $row) {
            try {
                $arr = explode("-", $row);
                $productId = $arr[0];
                $productVariantId=$arr[1];

                set_time_limit(6);
                /** @var  $shopHasProduct CShopHasProduct */
                $shopHasProduct = $shopHasProductRepo->findOneBy(['productId'=>$productId, 'productVariantId'=>$productVariantId ]);
                $shopIdOrigin=$shopHasProduct->shopId;
                /** @var  $productHasShopDestinationFind CProductHasShopDestination */
                $productHasShopDestinationFind = $productHasShopDestinationRepo->findOneBy(['productId'=>$productId,'productVariantId'=>$productVariantId,'shopIdOrigin'=>$shopIdOrigin,'shopIdDestination'=>$shopIdDestination]);
                if($productHasShopDestinationFind == null){
                        continue;
                }else{
                    $productHasShopDestinationFind->statusId=$status;
                    $productHasShopDestinationFind->update();
                    $i++;
                }
                \Monkey::app()->repoFactory->commit();
            } catch
            (\Throwable $e) {
                \Monkey::app()->repoFactory->rollback();
                throw $e;
            }
        }
        return $i;
    }

    public function put()
    {
        //RETRY
        $i = 0;
        foreach ($this->app->router->request()->getRequestData('rows') as $row) {
            $product = \Monkey::app()->repoFactory->create('Product')->findOneByStringId($row);
            $this->app->eventManager->triggerEvent('product.marketplace.change', ['productId' => $product->printId()]);
        }
        return $i;
    }


    public function delete()
    {
        $shopIdDestination = $this->app->router->request()->getRequestData('shop');


        $rows = $this->app->router->request()->getRequestData('rows');
        $productHasShopDestinationRepo = \Monkey::app()->repoFactory->create('ProductHasShopDestination');
        $res='';
        foreach ($rows as $row) {

            $arr = explode("-", $row);
            $productId = $arr[0];
            $productVariantId=$arr[1];

            $productHasShopDestinationFind = $productHasShopDestinationRepo->findOneBy(['productId'=>$productId,'productVariantId'=>$productVariantId,'shopIdDestination'=>$shopIdDestination]);
            if($productHasShopDestinationFind==null){
                continue;
            }else{

                $productHasShopDestinationFind->delete();
                $res.=$productId.'-'.$productVariantId." disassociato dallo shop <br>";
            }


        }
        return $res;
    }
}