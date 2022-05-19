<?php

namespace bamboo\blueseal\controllers;

use bamboo\blueseal\jobs\CStatisticsGenerateFilesForQlik;

/**
 * Class CProductListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CStatisticsExportFile extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "statistics_export_file";

    public function get()
    {

        $path = \Monkey::app()->rootPath() . \Monkey::app()->cfg()->fetch('paths', 'exportedStatistics');
        $fileName = \Monkey::app()->router->getMatchedRoute()->getComputedFilter('fileName');
        $fullPath = $path . $fileName;

        if (file_exists($fullPath)) {
            header('Content-Type: text/csv');
            readfile($fullPath);
        } else {
            \Monkey::app()->router->response()->raiseRoutingError();
        }
    }
}