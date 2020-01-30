<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CShipmentCostListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/01/2020
 * @since 1.0
 */
class CShipmentCostListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "shipment_cost_list";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/shipment_cost_list.php');

        $shops = \Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser();
        $dateTime = $this -> app -> router -> request() -> getRequestData('dateTime');
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