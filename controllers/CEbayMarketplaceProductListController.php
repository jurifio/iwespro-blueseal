<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CEbayMarketplaceProductListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/03/2019
 * @since 1.0
 */
class CEbayMarketplaceProductListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "ebay_marketplace_product";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/ebay_marketplace_product.php');
        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketplaceHasShop')->findBy(['isActive'=>1]);
        if(isset($_GET['accountid'])){
            $accountid=$_GET['accountid'];
        } else{
            $accountid=0;
        }
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'marketplaceAccount'=>$marketplaceAccount,
            'accountid'=>$accountid,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}