<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class COrderManageController
 * @package bamboo\blueseal\controllers
 */
class COrderManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "order_manage";

    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/order_manage.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";

        $repoStatus = $this->app->repoFactory->create('OrderStatus');
        $statuses = $repoStatus->findAll();
        $orderId =  $this->app->router->request()->getRequestData('order');
		\BlueSeal::dump($orderId);
	    $order = $this->app->repoFactory->create('Order')->findOne([$orderId]);
		\BlueSeal::dump($order);

        $em = $this->app->entityManagerFactory->create('Country');
        $counties= $em->findAll("limit 999","");
        $invoicePrint = $this->urls['base']."xhr/InvoiceAjaxController";
        $orderPrint = $this->urls['base'] . "xhr/OrderAjaxController";

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'order' => $order,
            'statuses' => $statuses,
            'countries' => $counties,
            'page'=>$this->page,
            'sidebar'=> $this->sidebar->build(),
            'invoicePrint' =>$invoicePrint,
            'orderPrint' => $orderPrint
        ]);
    }

    public function post()
    {
        throw new RedPandaException('POST non prevista');
        if($status = $this->request->getRequestData('order_status')){
            try {
                $code = $this->app->dbAdapter->select("OrderStatus",array('id'=>$status))->fetch()['code'];
                //controllare che code comincia per ORD
                if($this->request->getRequestData('orderId')) {
                    $order = $this->app->repoFactory->create("Order")->findOneBy(['id' => $this->request->getRequestData('orderId')]);
                    $order->status = $code;
                    $this->app->repoFactory->create("Order")->update($order);
                }
            } catch (\Exception $e) {
            $this->app->router->response()->raiseUnauthorized();
            }

           // $this->app->dbAdapter->update('Order',array('status'=>$code),array("id"=>$this->request->getRequestData('orderId')));
        }
        $this->get();
        return;
    }

    public function put()
    {
        if($this->request->getRequestData('payed' == 'true')) {
	        if(!$this->app->orderManager->pay($this->request->getRequestData('orderId'),true)) throw new RedPandaException('Error setting payed');
	        return true;
        }
        return false;
    }
}