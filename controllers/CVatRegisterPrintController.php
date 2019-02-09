<?php

namespace bamboo\blueseal\controllers;

use bamboo\domain\entities\CProduct;
use bamboo\ecommerce\views\VBase;
use bamboo\core\base\CArrayCollection;
use bamboo\core\base\CObjectCollection;
use bamboo\core\base\CStdCollectibleItem;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\blueseal\controllers\CBluesealXhrController;

/**
 * Class CVatRegisterPrintController
 * @package bamboo\blueseal\controllers
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
class CVatRegisterPrintController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "vat_registerprint";

    public function get()
    {
        $view = new VBase(array());

        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/vat_registerprint.php');
        function get_string_between($string, $start, $end){
            $string = ' ' . $string;
            $ini = strpos($string, $start);
            if ($ini == 0) return '';
            $ini += strlen($start);
            $len = strpos($string, $end, $ini) - $ini;
            return substr($string, $ini, $len);
        }


        //$this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $billingJournalId = $this->app->router->request()->getRequestData('BillingJournal');

        $billingJournalRepo = \Monkey::app()->repoFactory->create('BillingJournal');
        $billingJournal=$billingJournalRepo->findOneBy(['id'=>$billingJournalId]);
        $dateJournal = new \DateTime($billingJournal->date);
        $date = $dateJournal->format('d-m-Y');
        $dateFilter=$dateJournal->format('Y-m-d');
        $totalUeNetReceipt=money_format('%.2n',  $billingJournal->totalUeNetReceipt) . ' &euro;';
        $totalUeVatReceipt=money_format('%.2n',  $billingJournal->totalUeVatReceipt) . ' &euro;';
        $totalUeReceipt=money_format('%.2n',  $billingJournal->totalUeReceipt) . ' &euro;';
        $totalUeNetInvoice=money_format('%.2n',  $billingJournal->totalUeNetInvoice) . ' &euro;';
        $totalUeVatInvoice=money_format('%.2n',  $billingJournal->totalUeVatInvoice) . ' &euro;';
        $totalUeInvoice=money_format('%.2n',  $billingJournal->totalUeInvoice) . ' &euro;';
        $totalXUeNetInvoice=money_format('%.2n',  $billingJournal->totalXUeNetInvoice) . ' &euro;';
        $totalXUeVatInvoice=money_format('%.2n',  $billingJournal->totalXUeVatInvoice) . ' &euro;';
        $totalXUeInvoice=money_format('%.2n',  $billingJournal->totalXUeInvoice) . ' &euro;';
        $groupUeTextReceipt=$billingJournal->groupUeTextReceipt;
        $groupUeTextInvoice=$billingJournal->groupUeTextInvoice;
        $groupXUeTextInvoice=$billingJournal->groupXUeTextInvoice;
        $dateNow=new\DateTime();
        $datePrint=$dateNow->format('Y-m-d 00:00:00');
        $billingJournal->datePrint=$datePrint;
        $billingJournal->update();
        $sql = "SELECT invoiceText as invoiceText,
                      invoiceDate as invoiceDate
              FROM Invoice 
                
                WHERE invoiceDate between '" . $dateFilter . " 00:00:00' and '" . $dateFilter . " 23:59:59' ";
        $invoiceText='';
        $resultTextInvoice = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        forEach ($resultTextInvoice as $resultTextInvoices) {
            $stringInvoice=$resultTextInvoices['invoiceText'];
            $invoiceText .= get_string_between($stringInvoice, '<!--start-->', '<!--end-->');
            $invoiceText .='<div class="newpage">';
        }



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
                    'groupUeTextReceipt'=> $groupUeTextReceipt,
                    'groupUeTextInvoice'=> $groupUeTextInvoice,
                    'groupXUeTextInvoice'=> $groupXUeTextInvoice,
                    'invoiceText'=>$invoiceText,

                    'page' => $this->page,
                    'logo' => $this->app->cfg()->fetch("miscellaneous", "logo"),
                    'fiscalData' => $this->app->cfg()->fetch("miscellaneous", "fiscalData"),

                ]);



            return $renderRegister;
        }


}

