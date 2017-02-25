<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\io\CJsonAdapter;

/**
 * Class CLanguageController
 * @package bamboo\blueseal\controllers\ajax
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
class CLanguageController extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $contentEditorUrl = $this->app->router->request()->getRequestData()['contentEditorUrl'];
        $widgetId = $this->app->router->request()->getRequestData()['widgetId'];
        $widgetKey = $this->app->router->request()->getRequestData()['widgetKey'];
        $widgetPath = $this->app->cfg()->fetch('paths', 'app') . '/data/widget/';

        $repo = $this->app->repoFactory->create('Lang');
        $languages = $repo->findAll();

        $html = "<table class='table'><thead><tr><th>Lingue</th><th>Stato traduzione</th><th>&nbsp;</th></tr></thead><tbody>";
        foreach ($languages as $language) {
            if (file_exists($widgetPath . $widgetId . '.' . $language->lang . '.json')) {
                $json = new CJsonAdapter($widgetPath . $widgetId . '.' . $language->lang . '.json');
                if ($json->keyExists($widgetKey)) {
                    $status = 'presente';
                } else {
                    $status = 'non presente';
                }
            } else {
                $status = 'non presente';
            }
            $html .= "<tr>";
            $html .= "<td>" . $language->name . "</td>";
            $html .= "<td>" . $status . "</td>";
            $html .= "<td><a class='btn btn-default btn-success' href='".$contentEditorUrl.'traduci/'.$language->lang.'/'.$widgetId.'/'.$widgetKey."'>traduci</a></td>";
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";

        return $html;
    }
}