<?php
namespace bamboo\blueseal\controllers;

use bamboo\blueseal\business\CBlueSealPage;
use bamboo\blueseal\business\CBlueSealSidebar;
use bamboo\core\exceptions\RedPandaConfigException;
use bamboo\core\router\ARootController;
use bamboo\core\intl\CLang;
use bamboo\core\router\CInternalRequest;
use bamboo\core\router\CRouter;


/**
 * Class ARestrictedAccessRootController
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
abstract class ARestrictedAccessRootController extends ARootController
{
    /**
     * @var string
     */
    protected $fallBack;

    /**
     * @var string
     */
    protected $logFallBack;

    /**
     * @var CBlueSealPage
     */
    protected $page;

    /**
     * @var CBlueSealSidebar
     */
    protected $sidebar;

    /**
     * @var string
     */
    protected $pageSlug;

    /**
     * @param $action
     * @throws RedPandaConfigException
     * @return bool
     */
    public function createAction($action)
    {
        if (empty($this->fallBack)) {
            $this->fallBack = 'blueseal';
        }

        if (empty($this->logFallBack)) {
            $this->logFallBack = $this->fallBack;
        }

        $this->page = new CBlueSealPage($this->pageSlug,$this->app);
        $this->sidebar = new CBlueSealSidebar($this->app);

        $this->app->authManager->auth();
        
        if ($this->app->getUser()->getId()!=0) {
            if (!$this->checkPermission($this->page->getPermissionPath())) {
                try{
                    $url = $this->app->cfg()->fetch('urls',$this->fallBack);
                } catch(\Throwable $e) {
                    if(empty($url)) throw new RedPandaConfigException('Fallback Url not found');
                }
                $this->response->setBody("uh-uh, you can't come here you rascal");
                $this->response->autoRedirectTo($this->app->baseUrl(false).$this->app->cfg()->fetch('urls',$this->fallBack));
                return;
            }
        } else {
            $url = $this->app->cfg()->fetch('urls',$this->logFallBack);
            if (empty($url)) {
                throw new RedPandaConfigException('Fallback Url not found');
            }
            $this->response->setBody("hey, try login first");
            $this->response->autoRedirectTo($this->app->baseUrl(false).$this->app->cfg()->fetch('urls',$this->logFallBack));
            return;
        }

        $this->app->setLang(new CLang(1,'it'));

        return $this->{$action}(
            new CInternalRequest('',
                                 'it',
                                 $this->app->router->getMatchedRoute()->getComputedFilters(),
                                $this->request->getRequestData(),
                                $this->app->router->request()->getMethod())
        );
    }

    /**
     * @param $permissionPath
     * @return bool
     */
    public function checkPermission($permissionPath)
    {
        return $this->app->getUser()->hasPermissions($permissionPath);
    }

    public function __destruct() {}
}