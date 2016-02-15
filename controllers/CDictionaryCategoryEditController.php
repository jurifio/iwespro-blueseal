<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;


/**
 * Class CDictionaryCategoryEditController
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
class CDictionaryCategoryEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "dictionary_category_edit";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/dictionary_category_edit.php');
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $shopId = $this->app->router->getMatchedRoute()->getComputedFilter('shopId');
        $shopName = $this->app->repoFactory->create('Shop')->findOneBy(['id'=>$shopId]);

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'shopName' => $shopName->title,
            'shopId' => $shopId,
            'page'=>$this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}