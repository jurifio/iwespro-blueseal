<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CProductBatch;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductBatchDetailsListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/03/2018
 * @since 1.0
 */
class CProductBatchDetailsShopListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_batch_details_shop_list";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {

        $user = \Monkey::app()->getUser();
        $isWorker = $user->hasPermission('worker');
        $allShops = $user->hasPermission('allShops');
        $isShopUser = $user->hasPermission('/admin/product/edit');
        if(isset($_GET['shopId'])){
            $shopId=$_GET['shopId'];
        }else {
            $userHasShopRepo = \Monkey::app()->repoFactory->create('UserHasShop');
            $user = \Monkey::app()->getUser()->id;
            if ($allShops) {
                $shopId = 'all';
            } else {
                $userHasShop = $userHasShopRepo->findOneBy(['userId' => $user]);
                $shopId = $userHasShop->userId;
            }
        }




        //$productBatch = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=> $productBatchId]);

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/product_batch_details_shop_list.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'shopId' => $shopId,
            'isWorker' => $isWorker,
            'allShops' => $allShops,
            'isShopUser' =>$isShopUser
        ]);
    }
}