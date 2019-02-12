<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;

/**
 * Class CVatRegisterPrintAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/02/2019
 * @since 1.0
 */
class CVatRegisterPrintAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "vat_registerprint";

    public function get()
    {
        $view = new VBase(array());
        $this->page = new CBlueSealPage($this->pageSlug, $this->app);
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/vat_registerprint.php');

        $billingJournalId = $this->app->router->request()->getRequestData('BilingJournal');

        $billingJournalRepo = \Monkey::app()->repoFactory->create('BillingJournal');
        $billingJournal=$billingJournalRepo->findOneBy(['id'=>$billingJournalId]);
        $dateJournal = new \DateTime($billingJournal->date);
        $date = $dateJournal->format('d-m-Y');
        $totalUeNetReceipt=money_format('%.2n',  $billingJournal->totalUeNetReceipt) . ' &euro;';
        $totalUeVatReceipt=money_format('%.2n',  $billingJournal->totalUeVatReceipt) . ' &euro;';
        $totalUeReceipt=money_format('%.2n',  $billingJournal->totalUeReceipt) . ' &euro;';
        $totalUeNetInvoice=money_format('%.2n',  $billingJournal->totalUeNetInvoice) . ' &euro;';
        $totalUeVatInvoice=money_format('%.2n',  $billingJournal->totalUeVatInvoice) . ' &euro;';
        $totalUeInvoice=money_format('%.2n',  $billingJournal->totalUeInvoice) . ' &euro;';
        $totalXUeNetInvoice=money_format('%.2n',  $billingJournal->totalXUeNetInvoice) . ' &euro;';
        $totalXUeVatInvoice=money_format('%.2n',  $billingJournal->totalXUeVatInvoice) . ' &euro;';
        $totalXUeInvoice=money_format('%.2n',  $billingJournal->totalXUeInvoice) . ' &euro;';
        $dateNow=new\DateTime();
        $datePrint=$dateNow->format('d-m-Y');
        $billingJournal->datePrint=$datePrint;
        $billingJournal->update();


                $renderRegister = $view->render([
                    'app' => new CRestrictedAccessWidgetHelper($this->app),
                    'date'=>$date,
                    'totalUeNetReceipt' => $totalUeNetReceipt,
                    'totalUeVatReceipt' => $totalUeVatReceipt,
                    'totalUeReceipt' => $totalUeReceipt,
                    'totalUeNetInvoice' => $totalUeNetInvoice,
                    'totalUeVatInvoice' => $totalUeVatInvoice,
                    'totalUeInvoice' => $totalUeInvoice,
                    'totalXUeNetInvoice' => $totalXUeNetInvoice,
                    'totalXUeVatInvoice' => $totalXUeVatInvoice,
                    'totalXUeInvoice' => $totalXUeInvoice,
                    'page' => $this->page,
                    'logo' => $this->app->cfg()->fetch("miscellaneous", "logo"),
                    'fiscalData' => $this->app->cfg()->fetch("miscellaneous", "fiscalData"),

                ]);



            return $renderRegister;
        }


}

