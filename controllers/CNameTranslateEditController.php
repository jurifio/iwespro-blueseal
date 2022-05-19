<?php
namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CShop;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;


/**
 * Class CNameTranslateEditController
 * @package redpanda\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNameTranslateEditController extends CNameTranslateManageController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "name_translate_edit";

    /**
     * @throws \Exception
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     * @throws \bamboo\core\exceptions\RedPandaORMException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/name_translate_edit.php');

        $name = $this->app->router->request()->getRequestData('name');

        $langs = \Monkey::app()->repoFactory->create('Lang')->findBy(['isActive'=>1]);

        $name = \Monkey::app()->repoFactory->create('ProductName')->findBy(['name' => $name]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'name' => $name,
            'langs' => $langs,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}