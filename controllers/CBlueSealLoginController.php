<?php

namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\intl\CLang;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBlueSealLoginController
 * @package bamboo\blueseal\controllers
 */
class CBlueSealLoginController extends ARestrictedAccessRootController
{
    public function createAction($action)
    {
        $this->app->authManager->auth();
        if( $this->app->getUser()->getId() != 0 ) {
            if ($this->checkPermission($this->app,'/admin/dashboard')) {
                $this->response->setBody("uh-uh, you can't come here you rascal");
                $this->response->raiseUnauthorizedAccess($this->app->baseUrl(false));
                $this->response->sendHeaders();
                return;
            } else {
                if($this->request->getRequestData('redirect')){
                    $this->app->router->response()->autoRedirectTo($this->app->baseUrl(false).\Monkey::app()->router->request()->getRequestData('redirect'));
                }
                else
                {
                    $this->app->router->response()->autoRedirectTo($this->app->baseUrl(false).'/blueseal/dashboard');
                }
                //$this->app->router->response()->autoRedirectTo($this->app->baseUrl(false).'/blueseal/dashboard');
                return;
            }
        }
        $this->app->setLang(new CLang(1,'it'));
        $this->page = new CBlueSealPage('login',$this->app);
        return $this->{$action}();

    }

    /**
     * @param $permissionPath
     * @return bool
     */
    public function checkPermission($permissionPath){
        $hasPermission = false;
        try{
            $id = $this->app->rbacManager->perms()->pathId($permissionPath);
            if($this->app->getUser()->hasPermission($id)) $hasPermission = true;
        }catch (\Throwable $e){

        }
        return $hasPermission;
    }

    /**
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/index.php');
        return $view->render(array(
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page
        ));
    }

    /**
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function post()
    {
        return $this->get();
    }
}