<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CWidgetStructureParser;
use bamboo\core\exceptions\RedPandaFileException;
use bamboo\core\io\CJsonAdapter;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CHomepageContentEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/12/2015
 * @since 1.0
 */
class CHomepageContentEditController extends ARestrictedAccessRootController
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
        $view = new VBase(array());
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
            'widgetPath' => $this->app->rootPath().$this->app->cfg()->fetch('paths', 'public') . '/content/widget/',
            'assetPath' => 'https://' . $this->app->cfg()->fetch('paths', 'domain'),
            'sidebar' => $this->sidebar->build()
        ]);
    }

    /**
     * @throws RedPandaFileException
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function put()
    {
        $assetPath = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'store-theme');
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/content_homepage_edit.php');
        $appPath = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'public');

        $data = $this->app->router->request()->getRequestData();
        $files = $this->app->router->request()->getFiles();

        foreach ($files as $field => $file) {
            $name = pathinfo($file['name']);
            $uploadfile = rand(0, 9999999999) . '.' . $name['extension'];
            if (!rename($file['tmp_name'], $assetPath . '/assets/img/widgets/' . $uploadfile)) {
                throw new RedPandaFileException('Cannot write to %s', $file['name']);
            }
            $data[$field] = $file['name'];
        }

        $widgetId = $data['widgetId'];
        $widgetType = $data['widgetType'];
        $widgetLang = $data['widgetLang'];

        unset($data['widgetId'], $data['widgetType'], $data['widgetLang']);

        $json = new CJsonAdapter($appPath . '/content/widget/' . $widgetType . '.' . $widgetLang . '.json');

        $grid = [];
        foreach ($data as $widgetKey => $widgetKeyValue) {
            if ($widgetKeyValue == 'undefined') {
                continue;
            }

            if ($widgetKey == 'animation') {
                $widgetKeyValue = 'animation animated ' . $widgetKeyValue;
            }

            if (in_array($widgetKey, ['xs', 'sm', 'md', 'lg'])) {
                if ($widgetKeyValue != '') {
                    $grid[] = 'col-' . $widgetKey . '-' . $widgetKeyValue;
                }
            } else {
                $json->replace($widgetId . '\\' . $widgetKey, $widgetKeyValue);
            }
        }
        $json->replace($widgetId . '\\grid', implode(' ', $grid));

        try {
            $json->save();
            echo json_encode(['widget' => $widgetType,'key' => $widgetId,'lang' => $widgetLang,'status'=>'ok','message'=>'Widget aggiornato']);
        } catch (\Exception $e) {
            echo json_encode(['widget' => $widgetType,'key' => $widgetId,'lang' => $widgetLang,'status'=>'ko','message'=>$e->getMessage()]);
        }
    }
}