<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\ecommerce\APaymentGateway;
use bamboo\core\exceptions\BambooException;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class COrderAddController
 * @package bamboo\blueseal\controllers
 */
class COrderAddController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "order_add";

    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/order_add.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar'=> $this->sidebar->build(),
        ]);
    }

    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();

            $cart = $this->app->repoFactory->create('CartOrder')->getEmptyEntity();
            $cart->userId = $data['user'];
            $cart->orderPaymentMethodId = $data['orderPaymentMethod'];
            $cart->billingAddressId = $data['billingAddress'];
            $cart->shipmentAddressId = $data['shippingAddress'] ?? $data['billingAddress'];
            $cart->note = $data['note'];

            $billingAddress = $this->app->repoFactory->create('UserAddress')->findOneBy(['id'=>$cart->billingAddressId,'userId'=>$cart->userId]);
            $shippingAddress = $this->app->repoFactory->create('UserAddress')->findOneBy(['id'=>$cart->shipmentAddressId,'userId'=>$cart->userId]);

            $cart->frozenBillingAddress = $billingAddress->froze();
            $cart->frozenShippingAddress = $shippingAddress->froze();

            $cart->id = $cart->insert();
            $cart = $this->app->repoFactory->create('CartOrder')->findOne($cart->getIds());
            foreach ($data['orderLine'] as $line) {
                $sku = $this->app->repoFactory->create('ProductSku')->findOneByStringId($line);
                $this->app->cartManager->addSku($sku,1,$cart);
            }

            $coup = trim($data['coupon']);
            if(!empty($coup)) {
                $repo = $this->app->repoFactory->create('Coupon');
                $coupon = $repo->findOneBy(['valid'=>1,'code'=>$coup]);
                if($coupon == false) {
                    $coupon = $this->app->repoFactory->create('CouponEvent')->getCouponFromEvent($coup);
                }
                if ($coupon != false) {
                    if ($coupon->couponType->validForCartTotal>0) {
                        if($this->app->cartManager->calculateGrossTotal($cart) > $coupon->couponType->validForCartTotal) {
                            $cart->couponId = $coupon->id;
                        }
                    } else {
                        $cart->couponId = $coupon->id;
                    }
                    try {
                        $cart->update();
                    } catch (\Throwable $e) {
                        $this->app->router->response()->raiseUnauthorized();
                    }
                }
            }

            $order = $this->app->cartManager->customCartToOrder($cart);
            if(!$order) throw new BambooException('Errorissimo nel trasformare l\'ordine');

            /** @var APaymentGateway $gateway */
            $gateway = $this->app->orderManager->getPaymentGateway($order);

            $return = $order->toArray();
            if($url = $gateway->getUrl($order)) {
                $return['url'] = base64_encode($url);
            }
            if($data['mail'] == 'true') {
                $this->app->eventManager->triggerEvent('orderBack',['orderId'=>$order->id]);
            }
            return json_encode($return);
        } catch (\Throwable $e) {
            throw $e;
        }

    }
}