<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CDetailBatchTranslateListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/01/2019
 * @since 1.0
 */
class CDetailBatchTranslateListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "detail_batch_translate_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/detail_batch_translate_list.php');

        $langId = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('langId');
        $pb = \Monkey::app()->router->request()->getRequestData('pbId');

        $allShops = \Monkey::app()->getUser()->hasPermission('allShops');
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build(),
            'allShops' => $allShops,
            'lang' => $langId,
            'pb' => $pb
        ]);
    }
}