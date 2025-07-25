<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CAggregatorStatisticViewController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/01/2020
 * @since 1.0
 */

class CAggregatorStatisticViewController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "aggregator_statistic_view";
    
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/aggregator_statistic_view.php');
        $dateNow=new \DateTime();
        $dateEnd=$dateNow->format('d-m-Y 00:00:00');
        $dateStart= $dateNow->modify('-7 days');
        $dateStart=$dateStart->format('d-m-Y 00:00:00');


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'dateStart'=>$dateStart,
            'dateEnd' =>$dateEnd,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}