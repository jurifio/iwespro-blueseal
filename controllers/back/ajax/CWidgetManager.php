<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaFileException,
    bamboo\core\io\CJsonAdapter;

/**
 * Class CWidgetManager
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CWidgetManager extends AAjaxController
{
    public function get() {}

    public function put()
    {
        $assetPath = $this->app->cfg()->fetch('paths', 'store-theme');
        $appPath = $this->app->cfg()->fetch('paths', 'app');

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

        $json = new CJsonAdapter($appPath . '/data/widget/' . $widgetType . '.' . $widgetLang . '.json');

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
            return json_encode(['widget' => $widgetType,'key' => $widgetId,'lang' => $widgetLang,'status'=>'ok','message'=>'Widget aggiornato']);
        } catch (\Throwable $e) {
            return json_encode(['widget' => $widgetType,'key' => $widgetId,'lang' => $widgetLang,'status'=>'ko','message'=>$e->getMessage()]);
        }
    }

    public function post()
    {
        $assetPath = $this->app->cfg()->fetch('paths', 'store-theme');
        $appPath = $this->app->cfg()->fetch('paths', 'app');

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

        $json = new CJsonAdapter($appPath . '/data/widget/' . $widgetType . '.' . $widgetLang . '.json');

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
            return json_encode(['widget' => $widgetType,'key' => $widgetId,'lang' => $widgetLang,'status'=>'ok','message'=>'Widget aggiornato']);
        } catch (\Throwable $e) {
            return json_encode(['widget' => $widgetType,'key' => $widgetId,'lang' => $widgetLang,'status'=>'ko','message'=>$e->getMessage()]);
        }
    }

    public function delete() {}
}