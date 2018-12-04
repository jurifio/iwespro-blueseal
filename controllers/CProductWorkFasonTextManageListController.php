<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductWorkFasonTextManageListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/12/2018
 * @since 1.0
 */
class CProductWorkFasonTextManageListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_work_fason_text_manage_list";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/product_work_fason_text_manage_list.php');

        $pbtmId = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');

        /** @var CProductBatchTextManage $pbtm */
        $pbtm = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$pbtmId])->productBatchTextManage;


        $isWorker = \Monkey::app()->getUser()->hasPermission('worker');
        $allShops = \Monkey::app()->getUser()->hasPermission('allShops');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'pbtm' => $pbtm,
            'isWorker' => $isWorker,
            'allShops' => $allShops
        ]);
    }
}