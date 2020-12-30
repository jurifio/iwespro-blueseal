<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductShareHasShopDestinationEditController
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
class CProductShareHasShopDestinationEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "productshare_hasshopdestination_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/productshare_hasshopdestination_edit.php');
        $marketplaceAccountId =  \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');
        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$marketplaceAccountId]);
        $langSelectId=$marketplaceAccount->config['lang'];



        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'marketplaceAccount'=>$marketplaceAccount,
            'langSelectId'=>$langSelectId,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}