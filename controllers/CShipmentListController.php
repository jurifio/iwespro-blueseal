<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CProductSlimListController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/09/2016
 * @since 1.0
 */
class CShipmentListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "shipment_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/shipment_list.php');

        $shops = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $res = $this -> app -> dbAdapter -> query('SELECT shipmentInvoiceNumber as shipmentInvoiceNumber , dateInvoice as dateInvoice from Shipment where shipmentInvoiceNumber is not null 
and dateInvoice is not null order by dateInvoice ASc limit 1', []) -> fetchAll();

        foreach ($res as $result) {
            $shipmentInvoiceNumber = $result['shipmentInvoiceNumber'];
            // $invoiceDate= new \DateTime($result['dateInvoice']);
            $invoiceDate=strtotime($result['dateInvoice']);
        }
        $dateInvoice=date('Y-m-d\TH:i', $invoiceDate);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'shops' => $shops,
            'shipmentInvoiceNumber'=>$shipmentInvoiceNumber,
            'dateInvoice'=>$dateInvoice,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}