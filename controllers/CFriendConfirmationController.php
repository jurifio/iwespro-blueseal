<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\COrderLineManager;
use bamboo\blueseal\controllers\ARestrictedAccessRootController;
use bamboo\core\router\CRouter;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CFriendConfirmationController
 * @package bamboo\blueseal\controllers
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
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/order_confirm.php');
        $filters = $router->getMatchedRoute()->getComputedFilters();
        $orderLine = false;

        try{
            $orderLine = \Monkey::app()->repoFactory->create('OrderLine')->findOne(['id'=>$filters['orderLineId'],'orderId'=>$filters['orderId']]);
            if($filters['confirm'] == 'ook') {
                $success = \Monkey::app()->repoFactory->create('OrderLine')->friendConfirm($orderLine);
                $ok = true;
            } elseif ($filters['confirm'] == 'kko') {
                $success = \Monkey::app()->repoFactory->create('OrderLine')->friendRefuse($orderLine);
                $ok = false;
            }
            $error = !$success; //Just don't ask
        } catch(\Throwable $e) {
	        $this->app->applicationError("CFriendConfirmationController",'Error confirming',$e->getMessage(),$e);
            $error = true;
        }

        return $view->render(array(
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