<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\intl\CLang;
use bamboo\core\io\CJsonAdapter;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CLandingPageAddController
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
class CLandingPageAddController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "landing_add";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/landing_add.php');

        $brandRepo = \Monkey::app()->repoFactory->create('ProductBrand');
        $brandCollection = $brandRepo->findAll();

        $categoryCollection = $this->app->categoryManager->categories()->bidim();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'brandList' => $brandCollection,
            'categoryList' => $categoryCollection,
            'user' => $this->app->getUser(),
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function post()
    {
        $root = $this->app->cfg()->fetch('paths','root');
        $this->app->setLang(new CLang(1,'it'));
        $json = new CJsonAdapter($this->app->rootPath().$this->app->cfg()->fetch('paths','store-theme').'/layout/focusPage.it.json');
        $json->append('\\',$this->app->router->request()->getRequestData(), true);
        $json->save();
    }
}