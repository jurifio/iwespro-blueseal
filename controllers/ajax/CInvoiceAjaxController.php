<?php
namespace bamboo\blueseal\controllers\ajax;

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
class CInvoiceAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "invoice_print";
    protected $page;

    public function get()
    {
        $view = new VBase(array());
        $this->page = new CBlueSealPage($this->pageSlug, $this->app);
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/invoice_print.php');

        $orderId = $this->app->router->request()->getRequestData('orderId');

        $orderRepo = $this->app->repoFactory->create('Order');
        $order = $orderRepo->findOneBy(['id' => $orderId]);
        $invoiceRepo = $this->app->repoFactory->create('Invoice');
        $invoiceNew = $invoiceRepo->getEmptyEntity();
        $siteChar = $this->app->cfg()->fetch("miscellaneous","siteInvoiceChar");
        if ($order->invoice->isEmpty()) {
            try {
                $invoiceNew->orderId = $orderId;
                $today = new \DateTime();
                $invoiceNew->invoiceYear = $today->format('Y-m-d H:i:s');
                $year = (new \DateTime())->format('Y');
                $em = $this->app->entityManagerFactory->create('Invoice');

                $number = $em->query("SELECT ifnull(MAX(invoiceNumber),0)+1 as new
                                      FROM Invoice
                                      WHERE
                                      Invoice.invoiceYear = ? AND
                                      Invoice.invoiceType='F' AND
                                      Invoice.invoiceSiteChar= ?",[$year,$siteChar])->fetchAll()[0]['new'];

                $invoiceNew->invoiceNumber = $number;
                $invoiceNew->invoiceDate = $today->format('Y-m-d H:i:s');

                $invoiceRepo->insert($invoiceNew);
                $order = $orderRepo->findOneBy(['id' => $orderId]);
            } catch (\Exception $e) {
                throw $e;
                $this->app->router->response()->raiseProcessingError();
                $this->app->router->response()->sendHeaders();
            }
        }

            foreach ($order->invoice as $invoice) {
                if (is_null($invoice->invoiceText)) {
                    $userAddress = igbinary_unserialize($order->frozenBillingAddress);
                    $userAddress->setEntityManager($this->app->entityManagerFactory->create('UserAddress'));
	                if(!is_null($order->frozenShippingAddress)) {
		                $userShipping = igbinary_unserialize($order->frozenShippingAddress);
		                $userShipping->setEntityManager($this->app->entityManagerFactory->create('UserAddress'));
	                } else {
		                $userShipping = $userAddress;
	                }


                    $productRepo = $this->app->repoFactory->create('ProductNameTranslation');

                    $invoice->invoiceText = $view->render([
                        'app' => new CRestrictedAccessWidgetHelper($this->app),
                        'userAddress' => $userAddress,
                        'userShipping' => $userShipping,
                        'order' => $order,
                        'invoice' => $invoice,
                        'productRepo' => $productRepo,
                        'page' => $this->page,
                        'logo' => $this->app->cfg()->fetch("miscellaneous", "logo"),
                        'fiscalData' => $this->app->cfg()->fetch("miscellaneous", "fiscalData")
                    ]);
                    try {
                        $invoiceRepo->update($invoice);
                    } catch (\Exception $e) {
                        throw $e;
                        $this->app->router->response()->raiseProcessingError();
                        $this->app->router->response()->sendHeaders();
                    }
                }

                return $invoice->invoiceText;
            }
        }

}

