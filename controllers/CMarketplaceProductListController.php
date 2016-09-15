<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\events\EGenericEvent;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CProductListController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CMarketplaceProductListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "marketplace_product_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/marketplace_product_list.php');

        $q = "";
        if($this->app->router->request()->getRequestData('marketplaceId')) {
            $q.= "?marketplaceId=".$this->app->router->request()->getRequestData('marketplaceId');
            if($this->app->router->request()->getRequestData('marketplaceAccountId')) {
                $q.= "&marketplaceAccountId=".$this->app->router->request()->getRequestData('marketplaceAccountId');
            }

        }
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'queryString' => $q,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}