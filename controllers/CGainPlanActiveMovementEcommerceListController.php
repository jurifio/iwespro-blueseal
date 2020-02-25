<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CGainPlanActiveMovementEcommerceListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 25/02/2020
 * @since 1.0
 */

class CGainPlanActiveMovementEcommerceListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "gainplan_active_movement_ecommerce_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/gainplan_active_movement_ecommerce_list.php');

        $permission = \Monkey::app()->getUser()->hasPermission('allShops');

        /** LOGICA */
        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $pageURL = $blueseal."/gainplan/gainplan-ecommerce-attivo";

        $opera = $blueseal."/gainplan/gainplan-ecommerce-attivo";
        $aggiungi = $blueseal."/gainplan/gainplan-ecommerce-attivo";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'pageURL' =>$pageURL,
            'operaURL' =>$opera,
            'aggiungiURL' =>$aggiungi,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build(),
            'permission' => $permission
        ]);
    }
}