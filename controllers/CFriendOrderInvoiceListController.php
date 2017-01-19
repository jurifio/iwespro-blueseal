<?php
namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\asset\CAssetCollection;
use bamboo\core\router\CInternalRequest;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class COrderListController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CFriendOrderInvoiceListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "friend_order_invoice_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/friend_order_invoice_list.php');
        $allShops = \Monkey::app()->getUser()->hasPermission('allShops');

        /** LOGICA */
        $blueseal = $this->app->baseUrl(false).'/blueseal/';
        $pageURL = $blueseal . "orders";

        $opera = $blueseal . "order";
        $aggiungi = $blueseal . "order";
        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'pageURL' =>$pageURL,
            'operaURL' =>$opera,
            'aggiungiURL' =>$aggiungi,
            'page' => $this->page,
            'allShops' => $allShops,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}