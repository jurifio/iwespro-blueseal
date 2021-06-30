<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\entities\CShipment;
use bamboo\domain\entities\CShipmentFault;
use bamboo\domain\entities\COrderLineHasShipment;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;
use bamboo\domain\repositories\CCouponRepo;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CDispatchOrderMailToCustomer
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <juri@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/10/2019
 * @since 1.0
 */

class CDispatchOrderDeliveredToCustomer extends ACronJob
{

    var $success = "ORD_ARCH";
    var $fail = "ORD_SHIPPED";

    /**
     * @param null $args
     */
    public function run($args = null)
    {

        /** @var COrderRepo $orderRepo */
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $orders = $orderRepo->findBy(['status' => 'ORD_DELIVERED']);
        $homeShop = $this->app->cfg()->fetch("general","shop-id");
        $couponRepo=\Monkey::app()->repoFactory->create('Coupon');
        try {

            \Monkey::app()->repoFactory->beginTransaction();
            foreach ($orders as $order) {
                if (ENV === 'dev') {
                    $db_host = 'localhost';
                    $db_name = 'pickyshop_dev';
                    $db_user = 'root';
                    $db_pass = 'geh44fed';
                } else {
                    $db_host = '5.189.159.187';
                    $db_name = 'pickyshopfront';
                    $db_user = 'pickyshop4';
                    $db_pass = 'rrtYvg6W!';
                }
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }

                $user = \Monkey::app()->repoFactory->create('User')->findOneBy(['id' => $order->userId]);
                $langId = $order->user->langId;
                $lang = \Monkey::app()->repoFactory->create('Lang')->findOneBy(['id' => $langId]);
                $to = [$user->email];
                $orderLineHasShipment = \Monkey::app()->repoFactory->create("OrderLineHasShipment")->findOneBy(['orderId' => $order->id]);
                $findOrderLineShipped=\Monkey::app()->repoFactory->create('OrderLine')->findBy(['orderId'=>$order->id]);
                $okGo=0;
                foreach($findOrderLineShipped as $orderLineShipped){
                    if($orderLineShipped->status!='ORD_DELIVERED'){
                        $okGo=1;
                        break;
                    }else{
                        if ($orderLineShipped->isParallel == 1) {
                            $remoteIwesOrderId = $order->remoteIwesOrderId;
                            $stmtOrderLine = $db_con->prepare('UPDATE OrderLine SET `status`=\'' . $orderLineShipped->status . '\' WHERE 
                            productId=' . $orderLineShipped->productId . ' AND
                            productVariantId=' . $orderLineShipped->productVariantId . ' AND 
                            productSizeId=' . $orderLineShipped->productSizeId . ' AND  
                            remoteOrderSupplierId=' . $orderLineShipped->orderId . '
                            and orderId=' . $remoteIwesOrderId);
                            $stmtOrderLine->execute();

                        } else {
                            $stmtOrderLine = $db_con->prepare("UPDATE OrderLine SET `status`='" . $orderLineShipped->status . "' WHERE remoteOrderLineSellerId=" . $orderLineShipped->id . " and remoteOrderSellerId=" . $orderLineShipped->orderId . " and remoteShopSellerId=".$homeShop);
                            $stmtOrderLine->execute();

                        }
                    }
                }
                if ($okGo==1){
                    continue;
                } else {
                    $orderLine = \Monkey::app()->repoFactory->create("OrderLine")->findOneBy(['id' => $orderLineHasShipment->orderLineId,'orderId' => $order->id]);
                    $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $orderLine->productId,'productVariantId' => $orderLine->productVariantId]);
                    $productBrandId = $product->productBrandId;
                    $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => $homeShop]);
                    $sql = "SELECT DISTINCT p.id,p.productVariantId 
                FROM Product p join ShopHasProduct shp on p.id=shp.productId and p.productVariantId=shp.productVariantId where 
                 p.qty > 0 and 
                 p.productBrandId= ?               and   
                 shp.shopId=?       and                                                                                                        
                p.productStatusId in (6,11) limit 6";

                    $productBrand = $this->app->dbAdapter->query($sql,[$productBrandId,$shop->id])->fetchAll();
                    $shipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id' => $orderLineHasShipment->shipmentId]);

                    $urlSite = $shop->urlSite;
                    $logoSite = $shop->logoSite;
                    /** @var CCoupon $coupon */
                    $coupon = $couponRepo->createCouponFromType(1,$order->user->printId());
                    $couponCode = $coupon->code;

                    /** @var CEmailRepo $emailRepo */
                    $emailRepo = \Monkey::app()->repoFactory->create('Email');
                    $emailRepo->newPackagedMail('orderdeliveredtocustomer','no-reply@cartechinishop.com',$to,[],[],
                        ['order' => $order,'orderId' => $order->id,'shipment' => $shipment,'lang' => $lang->lang,'couponCode' => $couponCode,'logoSite' => $logoSite,'urlSite' => $urlSite,'productBrand' => $productBrand],'mailGun',null);
                    $order->status = 'ORD_ARCH';
                    $order->update();
                    if ($order->isParallel == 1) {
                        $remoteIwesOrderId = $order->remoteIwesOrderId;
                        $stmtOrder = $db_con->prepare('UPDATE `Order` SET `status`="ORD_ARCH" WHERE
                            remoteOrderSupplierId=' . $orderLineShipped->orderId . '
                            and orderId=' . $remoteIwesOrderId);
                        $stmtOrder->execute();

                    } else {
                        $stmtOrder = $db_con->prepare('UPDATE `Order` SET `status`="ORD_ARCH" WHERE remoteOrderSellerId="' . $order->id . '"  and remoteShopSellerId='.$homeShop);
                        $stmtOrder->execute();

                    }



                }
            }

            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e) {
            $this->app->router->response()->raiseUnauthorized();
            $res = $e->getMessage();
            $this->report('CDispatchOrderDeliveredToCustomer',$res);

        }

    }
}