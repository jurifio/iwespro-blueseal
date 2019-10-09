<?php
namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CChangeOrderStatus
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CInvoiceOnlyPrintAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "invoice_print";

    public function get()
    {
            $this->page = new CBlueSealPage($this->pageSlug, $this->app);

            $orderId = $this->app->router->request()->getRequestData('orderId');
            $invoiceShopId =  $this->app->router->request()->getRequestData('invoiceShopId');

            $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
            $invoice = $invoiceRepo->findOneBy(['id' => $orderId,'invoiceShopId'=>$invoiceShopId]);


                return $invoice->invoiceText;


        }


}

