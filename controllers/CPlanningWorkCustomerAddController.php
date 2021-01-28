<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanDetail;
use bamboo\domain\entities\CEditorialPlanSocial;
use bamboo\ecommerce\views\VBase;

/**
 * Class CPlanningWorkCalendarListController
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
class CPlanningWorkCustomerAddController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "planning_workcustomer_add";

    public function get()
    {


        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/planning_workcustomer_add.php');
        $user=$this->app->getUser()->getId();
        $userHasShop=\Monkey::app()->repoFactory->create('UserHasShop')->findOneBy(['userId'=>$user]);
        $billRegistryClientIdSelected='';
        $shopId='';
        if($userHasShop){

            $brca=\Monkey::app()->repoFactory->create('BillRegistryClientAccount')->findOneBy(['shopId'=>$userHasShop->shopId]);
            if($brca){
                $shopId=$userHasShop->shopId;
                $billRegistryClientIdSelected=$brca->billRegistryClientId;
            }
        }



        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'billRegistryClientIdSelected' => $billRegistryClientIdSelected,
            'shopId'=>$shopId,
            'sidebar' => $this->sidebar->build(),
        ]);
    }
}