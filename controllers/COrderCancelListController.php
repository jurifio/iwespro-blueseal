<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class COrderCancelListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/10/2019
 * @since 1.0
 */
class COrderCancelListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "order_cancellist";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/order_cancellist.php');

        /** LOGICA */
        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $pageURL = $blueseal."orders";

        $opera = $blueseal."order";
        $aggiungi = $blueseal."order";

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'pageURL' =>$pageURL,
            'operaURL' =>$opera,
            'aggiungiURL' =>$aggiungi,
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}