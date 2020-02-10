<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CShipmentInvoiceListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/02/2020
 * @since 1.0
 */
class CShipmentInvoiceListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "shipment_invoice_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/shipment_invoice_list.php');

        $shops = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $res = $this -> app -> dbAdapter -> query('SELECT shipmentInvoiceNumber as shipmentInvoiceNumber , dateInvoice as dateInvoice from Shipment where shipmentInvoiceNumber is not null 
and dateInvoice is not NULL  order by lastUpdate desc limit 1', []) -> fetchAll();

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