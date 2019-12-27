<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CEmailTemplateEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/12/2019
 * @since 1.0
 */
class CEmailTemplateEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "emailtemplate_edit";

    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/emailtemplate_edit.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";


            // recupero la newsletter

        $id =   \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');
        $emailTemplate = \Monkey::app()->repoFactory->create('EmailTemplate')->findOneBy(['id'=>$id]);
        $shops=\Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce'=>1]);
        $emailTemplateTranslation=\Monkey::app()->repoFactory->create('EmailTemplateTranslation')->findBy(['templateEmailId'=>$id]);
        //recupero l'evento newsletter
        $emailTemplateId =$emailTemplate->id;
        $emailTemplateName = $emailTemplate->name;
        $emailTemplateModel = $emailTemplate->template;
        $languages=[];
        $i=0;
        $larray=[];
        foreach($emailTemplateTranslation as $ett) {
            $langs = \Monkey::app()->repoFactory->create('Lang')->findOneBy(['id' => $ett->langId]);

            $lg = ['id' => $ett->id, 'lang' => $langs->lang,'name' => $langs->name,'value' => $ett->template];
            array_push($languages,$lg);
            $i++;
            array_push($larray,$ett->id);
        }

        $arrayl=implode('-',$larray);



        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'emailTemplate' => $emailTemplate,
            'page'=>$this->page,
            'shops'=>$shops,
            'languages'=>$languages,
            'arrayl'=>$arrayl,
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