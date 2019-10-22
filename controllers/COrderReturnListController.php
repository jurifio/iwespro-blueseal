<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class COrderReturnListController
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
class COrderReturnListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "order_returnlist";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/order_returnlist.php');

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