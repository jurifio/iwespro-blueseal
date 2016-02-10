<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use redpanda\blueseal\business\COrderLineManager;
use redpanda\blueseal\controllers\ARestrictedAccessRootController;
use bamboo\core\router\CRouter;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CFriendConfirmationController
 * @package redpanda\blueseal\controllers
 */
class CFriendConfirmationController extends ARestrictedAccessRootController
{
    protected $fallBack = "home";
    protected $logFallBack = "blueseal";
    protected $pageSlug = "order_friend_confirmation";


    public function get()
    {
        $view = new VBase(array());
        $error = false;
        $ok = false;

        /** @var CRouter $router */
        $router = $this->app->router;
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/order_confirm.php');
        $filters = $router->getMatchedRoute()->getComputedFilters();
        $orderLine = false;

        try{
            $repo = $this->app->repoFactory->create('OrderLine');
            $orderLine = $repo->findOne(['id'=>$filters['orderLineId'],'orderId'=>$filters['orderId']]);
            $error = true;
            $om = new COrderLineManager($this->app,$orderLine);
            if($orderLine->status == 'ORD_FRND_SENT'){
                $error = false;
                if($filters['confirm'] == 'ook'){
                    $ok = true;
                    $om->changeStatus($om->nextOk());
                }elseif($filters['confirm'] == 'kko'){
                    $ok = false;
                    $om->changeStatus($om->nextErr());
                }
            }

        } catch(\Exception $e) {
            $error = true;
        }

        echo $view->render(array(
            'error' => $error,
            'page'=> $this->page,
            'confirm'=> $ok,
            'orderLine'=> $orderLine,
            'sidebar'=> $this->sidebar->build(),
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'data' => $this->request->getUrlPath()
        ));
    }

    public function post()
    {

    }
}