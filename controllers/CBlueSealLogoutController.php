<?php

namespace bamboo\blueseal\controllers;



/**
 * Class CBlueSealLogoutPageController
 * @package bamboo\blueseal\controllers;
 */
class CBlueSealLogoutController extends ARestrictedAccessRootController
{
    public function createAction($action)
    {
        $this->app->authManager->auth();
        $this->app->authManager->logout();
        return $this->{$action}();
    }

    public function get()
    {
        $home = $this->app->baseUrl(false);
        $this->app->router->response()->autoRedirectTo($home.'/blueseal');
    }
}