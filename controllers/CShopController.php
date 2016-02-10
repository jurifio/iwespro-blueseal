<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CShopController
 * @package redpanda\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CShopController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "shop_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths', 'blueseal') . '/template/shop_list.php');

        $shops = $this->app->repoFactory->create('Shop')->findAll();

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'shops' => $shops,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}