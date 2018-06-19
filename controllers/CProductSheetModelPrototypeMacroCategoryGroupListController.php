<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CUser;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductSheetModelPrototypeMacroCategoryGroupListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/06/2018
 * @since 1.0
 */
class CProductSheetModelPrototypeMacroCategoryGroupListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_sheet_model_prototype_macro_category_group";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_sheet_model_prototype_macro_category_group.php');

        /** @var CUser $user */
        $user = \Monkey::app()->getUser();

        $fullPerm = $user->hasPermission('allShops');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'fullPerm' => $fullPerm,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}