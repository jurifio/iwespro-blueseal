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
class CNewsletterUserEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "newsletteruser_edit";

    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/newsletteruser_edit.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";


            // recupero la newsletter
        $repoNewsletter = \Monkey::app()->repoFactory->create('Newsletter');
        $newsletterStatus = $repoNewsletter->findAll();
        $newsletterId =  $this->app->router->request()->getRequestData('newsletter');
        $newsletter = \Monkey::app()->repoFactory->create('Newsletter')->findOne([$newsletterId]);

        //recupero l'evento newsletter
        $newsletterEventId =$newsletter->newsletterEventId;
        $repoNewsletterEvent =\Monkey::app()->repoFactory->create('NewsletterEvent');
        $newsletterEventStatus =$repoNewsletterEvent->findAll();
        $newsletterEvent = \Monkey::app()->repoFactory->create('NewsletterEvent')->findOne([$newsletterEventId]);

        //recupero la campagna associata
        $newsletterCampaignId =$newsletter->newsletterCampaignId;
        $repoNewsletterCampaign =\Monkey::app()->repoFactory->create('NewsletterCampaign');
        $newsletterCampaignStatus =$repoNewsletterCampaign->findAll();
        $newsletterCampaign=\Monkey::app()->repoFactory->create('NewsletterCampaign')->findOne([$newsletterCampaignId]);

        //recupero la lista destinatari associata;

        $newsletterEmailListId=$newsletter->newsletterEmailListId;
        $repoNewsletterEmailList =\Monkey::app()->repoFactory->create('NewsletterEmailList');
        $newsletterEmailListStatus =$repoNewsletterEmailList->findAll();
        $newsletterEmailList =\Monkey::app()->repoFactory->create('NewsletterEmailList')->findOne([$newsletterEmailListId]);
        // recupero il template

        $newsletterTemplateId = $newsletter->newsletterTemplateId;
        $repoNewsletterTemplate =\Monkey::app()->repoFactory->create('NewsletterTemplate');
        $newsletterTemplateStatus =$repoNewsletterTemplate->findAll();
        $newsletterTemplate =\Monkey::app()->repoFactory->create('NewsletterTemplate')->findOne([$newsletterTemplateId]);











        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'newsletter' => $newsletter,
            'newsletterEvent' => $newsletterEvent,
            'newsletterCampaign' => $newsletterCampaign,
            'newsletterEmailList' => $newsletterEmailList,
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