<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CProductSheetPrototype;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CModifyProductSheetPrototypeListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/05/2018
 * @since 1.0
 */
class CModifyProductSheetPrototypeListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "modify_product_sheet_prototype";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {

        $psId = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');

        /** @var CProductSheetPrototype $psp */
        $psp = \Monkey::app()->repoFactory->create('ProductSheetPrototype')->findOneBy(['id'=>$psId]);

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/modify_product_sheet_prototype.php');


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'psp' => $psp
        ]);
    }
}