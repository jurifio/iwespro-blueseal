<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CAggregatorAccountShopEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/09/2018
 * @since 1.0
 */
class CAggregatorAccountShopEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "aggregator_accountshop_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/aggregator_accountshop_edit.php');
        $marketplaceHasShopId =  \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');
        $marketplaceHasShop=\Monkey::app()->repoFactory->create('AggregatorHasShop')->findOneBy(['id'=>$marketplaceHasShopId]);
        $marketplace=\Monkey::app()->repoFactory->create('Marketplace')->findOneBy(['id'=>$marketplaceHasShop->marketplaceId]);




        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'marketplaceHasShop'=>$marketplaceHasShop,
            'page' => $this->page,
            'marketplace'=>$marketplace,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}