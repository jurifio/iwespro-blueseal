<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CProductBatch;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductBrandBatchDetailsListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/06/2018
 * @since 1.0
 */
class CProductBrandBatchDetailsListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_brand_batch_details_list";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {

        $productBatchId = $this->app->router->getMatchedRoute()->getComputedFilter('id');

        $superUser = \Monkey::app()->getUser()->hasPermission('allShops');

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/product_brand_batch_details_list.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'allShops' => $superUser,
            'sidebar' => $this->sidebar->build(),
            'productBatchId' => $productBatchId
        ]);
    }
}