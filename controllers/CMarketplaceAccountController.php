<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CShopListController
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
class CMarketplaceAccountController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "marketplace-account_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/marketplace_account_edit.php');
        $marketplaceAccountGet=\Monkey::app()->router->request()->getRequestData('id');
        $marketplaceCode=explode('-',$marketplaceAccountGet);


        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneBy(['id'=>$marketplaceCode[0],'marketplaceId'=>$marketplaceCode[1]]);

        $marketplaceConfig=json_encode($marketplaceAccount->config,false);
        $countConfig=json_decode($marketplaceConfig,true);
        $countField=count($countConfig);


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'marketplaceAccountGet' =>$marketplaceAccountGet,
            'marketplaceAccount'=>$marketplaceAccount,
            'marketplaceConfig'=>$marketplaceConfig,
            'marketplaceCode'=>$marketplaceCode,
            'countField'=>$countField,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}