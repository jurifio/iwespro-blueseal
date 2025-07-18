<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CDictionaryBrandEditController
 * @package bamboo\blueseal\controllers
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
class CDictionaryBrandEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "dictionary_brand_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/dictionary_brand_edit.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $shopId = $this->app->router->getMatchedRoute()->getComputedFilter('shopId');
        $shopName = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$shopId]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'shopName' => $shopName->title,
            'shopId' => $shopId,
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}