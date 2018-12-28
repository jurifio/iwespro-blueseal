<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CMarketplaceHasProductAssociateSaleListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/12/2018
 * @since 1.0
 */
class CMarketplaceHasProductAssociateSaleListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "marketplace_product_associatesale_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/marketplace_product_associatesale_list.php');


        $id = \Monkey::app()->router->request()->getRequestData('id');

        $q = "";
        if($this->app->router->request()->getRequestData('accountId')) {
            $q.= "?accountId=".$this->app->router->request()->getRequestData('accountId');
        }
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'id' => $id,
            'page' => $this->page,
            'queryString' => $q,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}