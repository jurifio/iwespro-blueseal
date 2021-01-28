<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CPlanningWorkListController
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
class CPlanningWorkCustomerListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "planning_workcustomer_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/planning_workcustomer_list.php');
        $user=$this->app->getUser()->getId();
        $userHasShop=\Monkey::app()->repoFactory->create('UserHasShop')->findOneBy(['userId'=>$user]);
        $billRegistryclientid='';
        $shopId='';
        if($userHasShop){

            $brca=\Monkey::app()->repoFactory->create('BillRegistryClientAccount')->findOneBy(['shopId'=>$userHasShop->shopId]);
            if($brca){
                $shopId=$userHasShop->shopId;
                $billRegistryclientid=$brca->billRegistryClientId;
            }
        }

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'shopId'=>$shopId,
            'billRegistryClientId'=>$billRegistryclientid,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}