<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\nestedCategory\CCategoryManager;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CProductEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductPriceManageController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "product_price_management";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/product_price_management.php');

		/** LOGICA */

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'shops' => $this->app->repoFactory->create('Shop')->findAll(),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}