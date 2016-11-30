<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

class CStatisticsExport extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "statistics_export";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/statistics_export.php');
        $path = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'exportedstatistics');
        $files = scandir($path);

        $filename = [];
        unset($files['.']);
        unset($files['..']);

        /** LOGICA */

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            // ---
            'files' => $files,
        ]);
    }
}