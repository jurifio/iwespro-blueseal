<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class COrderManageController
 * @package bamboo\blueseal\controllers
 */
class COrderLineManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $permission = "/admin/order/list";

    /**
     * @param CInternalRequest $request
     */
    public function get(CInternalRequest $request)
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/manage_order_line.php');
        $order = null;
        $repoStatus = $this->app->repoFactory->create('OrderLineStatus');

        $em = $this->app->entityManagerFactory->create('OrderLine');
        $orderLine = $em->findOne(array("id"=>$request->getFilter('orderLineId'),"orderId"=>$_GET['orderId']));

        $statuses = $repoStatus->listByPossibleStatuses($orderLine->status);

        $repo = $this->app->repoFactory->create('User');
        $order->user = $repo->em()->findOne(array("id"=>$order->userId));

        $em = $this->app->entityManagerFactory->create('Country');
        $counties= $em->findAll("limit 999","");

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'order' => $order,
            'statuses' => $statuses,
            'countries' => $counties
        ]);
    }

    public function post()
    {
        if ($status = $this->request->getRequestData('Order_status')) {
            $code = $this->app->dbAdapter->select("OrderStatus", array('id' => $status))->fetch()['code'];
            //controllare che code comincia per ORD
            $id = $this->request->getRequestData('orderId');
            if (!is_null($id)) {
                try {

                    $order = $this->app->repoFactory->create("Order")->findOneBy(['id' => $id]);
                    $order->status = $code;
                    $this->app->repoFactory->create("Order")->update($order);

                } catch (\Exception $e) {
                    $this->app->router->response()->raiseUnauthorized();
                }
                //$this->app->dbAdapter->update('Order',array('status'=>$code),array("id"=>$this->request->getRequestData('orderId')));
            }
            $this->get();
            return;
        }
    }

    public function put()
    {
        //cambia stato?
    }
}
