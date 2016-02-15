<?php

namespace bamboo\blueseal\controllers

use bamboo\blueseal\controllers\ajax\AAjaxController;

/**
 * Class CBluesealXhrController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CBluesealXhrController extends ARestrictedAccessRootController
{
    protected $fallBack = "home";
    protected $logFallBack = "blueseal";
    protected $pageSlug = "xhr_controller";
    /**
     * @return bool
     * @throws \Exception
     */
    public function reroute()
    {
        $controllerClass = $this->app->router->getMatchedRoute()->getComputedFilters()['xcontroller'];
        $widgetController = "bamboo\\blueseal\\controllers\\ajax\\C".ucfirst($controllerClass)."";

        if (class_exists($widgetController)) {
            /** @var AAjaxController $ctrl */
            $ctrl = new $widgetController($this->app);
        } else{
            $this->response->setBody('widget route not found _ 1');
            $this->response->raiseRoutingError()->sendHeaders();
            return false;
        }

        /** Check and execute method */
        if (method_exists($ctrl,$this->app->router->request()->getMethod())){
            $this->response->setBody($ctrl->createAction($this->app->router->request()->getMethod()));
        } else {
            $this->response->setBody('method not found _ 2');
            $this->response->raiseRoutingError()->sendHeaders();
            return false;
        }

        $this->response->sendHeaders();
        echo $this->response->getBody();
        return true;
    }

    /**
     * @return bool
     */
    public function get()
    {
        return $this->reroute();
    }

    /**
     * @return bool
     */
    public function post()
    {
        return $this->reroute();
    }

    /**
     * @return bool
     */
    public function put()
    {
        return $this->reroute();
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return $this->reroute();
    }

    /**
     * @param $path
     * @return string
     */
    protected function rewriteAssetUrl($path)
    {
        $pos = strrpos($path,'/');
        if((bool) $pos){
            $name = substr($path, $pos+1);
            return $this->app->baseUrl().'/assets/'.$name;
        } else {
            return $path;
        }
    }
}