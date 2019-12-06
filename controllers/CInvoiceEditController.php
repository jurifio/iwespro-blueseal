<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;

/**
 * Class CInvoiceEditController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/12/2019
 * @since 1.0
 */
class CInvoiceEditController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "invoice_edit";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/invoice_edit.php');
        $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $orderRepo=\Monkey::app()->repoFactory->create('Order');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $id =  \Monkey::app()->router->getMatchedRoute()->getComputedFilter('id');
        $invoice=$invoiceRepo->findOneBy(['id'=>$id]);
        $order=$orderRepo->findOneBy(['id'=>$invoice->orderId]);
        $shop=$shopRepo->findOneBy(['id'=>$invoice->invoiceShopId]);

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page'=>$this->page,
            'invoice'=>$invoice,
            'order'=>$order,
            'shop'=>$shop,
            'sidebar' => $this->sidebar->build()
        ]);

    }
}