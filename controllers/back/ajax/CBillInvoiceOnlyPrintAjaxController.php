<?php
namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBillInvoiceOnlyPrintAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/02/2020
 * @since 1.0
 */
class CBillInvoiceOnlyPrintAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "bill_invoice_print";

    public function get()
    {
            $this->page = new CBlueSealPage($this->pageSlug, $this->app);


         $invoiceId =  $this->app->router->request()->getRequestData('invoiceId');
            $BillRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
            $invoice = $BillRegistryInvoiceRepo->findOneBy(['id' => $invoiceId]);


                return $invoice->invoiceText;


        }


}

