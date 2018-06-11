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
class CNewsletterTemplateEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "newslettertemplate_edit";

    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/newslettertemplate_edit.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";


            // recupero la newsletter

        $id =  $this->app->router->request()->getRequestData('id');
        $newsletterTemplate = \Monkey::app()->repoFactory->create('NewsletterTemplate')->findOne([$id]);

        //recupero l'evento newsletter
        $newsletterTemplateId =$newsletterTemplate->id;
        $newsletterTemplateName = $newsletterTemplate->name;
        $newsletteTemplateModel = $newsletterTemplate->template;



        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'newsletterTemplate' => $newsletterTemplate,
            'page'=>$this->page,
            'sidebar'=> $this->sidebar->build(),

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
                    $order = \Monkey::app()->repoFactory->create("Order")->findOneBy(['id' => $this->request->getRequestData('orderId')]);
                    \Monkey::app()->repoFactory->create('Order')->updateStatus($order, $code);
                    $order->update();

                    \Monkey::app()->eventManager->triggerEvent('changeOrderNonPrevista',
                        [
                            'order' => $orderLine,
                            'status' => $this->success
                        ]);
                }
            } catch (\Throwable $e) {
                $this->app->router->response()->raiseUnauthorized();
            }

            // $this->app->dbAdapter->update('Order',array('status'=>$code),array("id"=>$this->request->getRequestData('orderId')));
        }
        $this->get();
        return;
    }

    public function put()
    {
        if($this->request->getRequestData('paidAmount' == 'true')) {
            if(!$this->app->orderManager->pay($this->request->getRequestData('orderId'),true)) throw new RedPandaException('Error setting paidAmount');
            return true;
        }
        return false;
    }
}