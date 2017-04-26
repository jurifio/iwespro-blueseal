<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\ecommerce\APaymentGateway;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\repositories\CCartRepo;
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

    /**
     * FIXME
     */
    public function post()
    {
        try {
            $data = $this->app->router->request()->getRequestData();

            /** @var CCartRepo $cartRepo */
            $cartRepo = $this->app->repoFactory->create('Cart');
            $cart = $cartRepo->getEmptyEntity();
            $cart->userId = $data['user'];
            $cart->cartTypeId = 5;
            $cart->orderPaymentMethodId = $data['orderPaymentMethod'];
            $cart->billingAddressId = $data['billingAddress'];
            $cart->shipmentAddressId = $data['shippingAddress'] ?? $data['billingAddress'];
            $cart->smartInsert();

            foreach ($data['orderLine'] as $line) {
                $sku = $this->app->repoFactory->create('ProductSku')->findOneByStringId($line);
                $cartRepo->addSku($sku,1,$cart);
            }

            $cartRepo->setCouponCodeToCart($data['coupon']);

            $order = $cartRepo->customCartToOrder($cart);
            if(!$order) throw new BambooException('Errorissimo nel trasformare l\'ordine');

            $order->note = $data['note'];
            $order->update();

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