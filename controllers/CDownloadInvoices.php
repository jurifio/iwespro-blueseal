<?php
namespace bamboo\blueseal\controllers;

use bamboo\blueseal\business\CBlueSealPage;
use bamboo\blueseal\business\CDownloadFileFromDb;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\exceptions\BambooRoutingException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CInvoiceNew;
use bamboo\ecommerce\views\VBase;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class CCouponListController
 * @package bamboo\app\controllers
 */
class CDownloadInvoices extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "download_invoices";

    public function get()
    {
        $filters = \Monkey::app()->router->getMatchedRoute()->getComputedFilters();
        $i = \Monkey::app()->repoFactory->create('InvoiceNew')->findOne([$filters['id']]);
        try {
            $user = \Monkey::app()->getUser();
            if (!$user->hasShop($i->shopAddressBook->shop->id)) throw new BambooRoutingException('Not Authorized');
            if (!$i) throw new BambooRoutingException('File Not Found');
            if ($i->invoiceType->printTemplateName) {
                $ret = $this->printOutInvoice($i);
            } elseif ($i->invoiceBin) {
                $download = new CDownloadFileFromDb(ucfirst('InvoiceBin'), 'invoiceId', $filters['id']);
                $ret = $download->getFile();
            }
            echo $ret;
        } catch (BambooRoutingException $e) {
            if ('File Not Found' === $e->getMessage()) \Monkey::app()->router->response()->raiseRoutingError();
            elseif ('Not Authorized' === $e->getMessage()) \Monkey::app()->router->response()->raiseUnauthorized();
        }
    }

    public function printOutInvoice(CInvoiceNew $i)
    {
        $templateName = $i->invoiceType->printTemplateName;
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/' . $templateName . '.php');
        $invoiceGivenShopBillingAddress = $i->shopAddressBook;
        if (!$invoiceGivenShopBillingAddress) {
            \Monkey::app()->applicationError('DownloadInvoices', 'printOutInvoice', 'Indirizzo di fatturazione specificato nella fattura non presente');
            throw new BambooRoutingException('File Not Found');
        }
        $mainShopBillingAddress = \Monkey::app()->repoFactory->create('Shop')->getMainShop()->billingAddressBook;

        if ($i->invoiceType->isActive) {
            if ($i->shopAddressBook) throw new BambooException('Non trovo l\'inidirizzo del negozio da inserire nella fattura');
            $addressBookEmitter = $mainShopBillingAddress;
            $addressBookRecipient = $invoiceGivenShopBillingAddress;
        } else {
            $addressBookEmitter = $invoiceGivenShopBillingAddress;
            $addressBookRecipient = $mainShopBillingAddress;
        }

        $imponibili = [];

        foreach($i->invoiceLine as $v) {
            $calculatedVat = SPriceToolbox::vatFromNetPrice($v->priceNoVat, $v->vat);
            if (array_key_exists($v->vat, $imponibili)) $imponibili[$v->vat] += $calculatedVat;
            else $imponibili[$v->vat] = $calculatedVat;
        }



        $html = $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'addressBookEmitter' => $addressBookEmitter,
            'addressBookRecipient' => $addressBookRecipient,
            'invoice' => $i,
            'imponibili' => $imponibili,
            'noVatTotal' => array_sum($imponibili),
            'page' => new CBlueSealPage($this->pageSlug, $this->app),
            'logo' => '//' //@TODO: da implementare //$this->app->cfg()->fetch("miscellaneous", "logo"),
        ]);
        return $html;
    }
}