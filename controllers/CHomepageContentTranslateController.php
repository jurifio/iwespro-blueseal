<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\blueseal\business\CWidgetStructureParser;
use bamboo\core\io\CJsonAdapter;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CHomepageContentTranslateController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/12/2015
 * @since 1.0
 */
class CHomepageContentTranslateController extends ARestrictedAccessRootController
{
    /**
     * @var string
     */
    protected $fallBack = "blueseal";

    /**
     * @var string
     */
    protected $pageSlug = "content_homepage_edit";

    /**
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {
        $widgetType = $this->app->router->getMatchedRoute()->getComputedFilter('type');
        $widgetId = $this->app->router->getMatchedRoute()->getComputedFilter('id');
        $widgetLang = $this->app->router->getMatchedRoute()->getComputedFilter('wlang');
        $widgetFile = $this->app->cfg()->fetch('paths','app').'/data/widget/'.$widgetType.'.'.$widgetLang.'.json';

        if (file_exists($widgetFile)) {
            $json = new CJsonAdapter($widgetFile);
            if ($json->keyExists($widgetId)) {
                $this->gotoEdit();
            } else {
                $this->gotoAdd();
            }
        } else {
            $this->gotoAdd();
        }
    }

    /**
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function gotoEdit()
    {
        $this->pageSlug = "content_homepage_edit";
        $this->page = new CBlueSealPage($this->pageSlug,$this->app);

        $view = new VBase();
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/content_homepage_edit.php');

        $repo = $this->app->repoFactory->create('Lang');
        $languages = $repo->findAll();

        $parser = new CWidgetStructureParser($this->app, $languages, 'homepage');

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'structure' => $parser,
            'widgetType' => $this->app->router->getMatchedRoute()->getComputedFilter('type'),
            'widgetId' => $this->app->router->getMatchedRoute()->getComputedFilter('id'),
            'widgetLang' => $this->app->router->getMatchedRoute()->getComputedFilter('wlang'),
            'widgetPath' => $this->app->cfg()->fetch('paths', 'app') . '/data/widget/',
            'assetPath' => 'http://' . $this->app->cfg()->fetch('paths', 'domain'),
            'sidebar' => $this->sidebar->build()
        ]);
    }

    /**
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function gotoAdd()
    {
        $this->pageSlug = "content_homepage_add";
        $this->page = new CBlueSealPage($this->pageSlug,$this->app);

        $view = new VBase();
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/content_homepage_add.php');

        $repo = $this->app->repoFactory->create('Lang');
        $languages = $repo->findAll();

        $parser = new CWidgetStructureParser($this->app, $languages, 'homepage');

        echo $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'structure' => $parser,
            'widgetType' => $this->app->router->getMatchedRoute()->getComputedFilter('type'),
            'widgetId' => $this->app->router->getMatchedRoute()->getComputedFilter('id'),
            'widgetLang' => $this->app->router->getMatchedRoute()->getComputedFilter('wlang'),
            'widgetPath' => $this->app->cfg()->fetch('paths', 'app') . '/data/widget/',
            'assetPath' => 'http://' . $this->app->cfg()->fetch('paths', 'domain'),
            'sidebar' => $this->sidebar->build()
        ]);
    }
}