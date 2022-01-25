<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CShopConfigEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CShopConfigEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "shopconfig_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/shopconfig_edit.php');
        $shopConfigId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        if(ENV=='dev') {
            $shopConfig =\Monkey::app()->repoFactory->create('ShopConfigDev')->findOneBy(['id'=>$shopConfigId]);
            $shop=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$shopConfig->shopId]);
            }else{
            $shopConfig =\Monkey::app()->repoFactory->create('ShopConfigProd')->findOneBy(['id'=>$shopConfigId]);
            $shop=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$shopConfig->shopId]);
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'shopConfig'=>$shopConfig,
            'shop'=>$shop,
            'sidebar' => $this->sidebar->build()
        ]);
    }
    public function post()
    {

    }
    public function put()
    {

    }
}