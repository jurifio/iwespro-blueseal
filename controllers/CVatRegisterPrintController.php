<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CUserAddress;
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
        $totalUeNetReceipt=number_format(  $billingJournal->totalUeNetReceipt,2,'.') . ' &euro;';
        $totalUeVatReceipt=number_format(  $billingJournal->totalUeVatReceipt,2,'.') . ' &euro;';
        $totalUeReceipt=number_format(  $billingJournal->totalUeReceipt,2,'.') . ' &euro;';
        $totalUeNetInvoice=number_format(  $billingJournal->totalUeNetInvoice,2,'.') . ' &euro;';
        $totalUeVatInvoice=number_format(  $billingJournal->totalUeVatInvoice,2,'.') . ' &euro;';
        $totalUeInvoice=number_format(  $billingJournal->totalUeInvoice,2,'.') . ' &euro;';
        $totalXUeNetInvoice=number_format(  $billingJournal->totalXUeNetInvoice,2,'.') . ' &euro;';
        $totalXUeVatInvoice=number_format(  $billingJournal->totalXUeVatInvoice,2,'.') . ' &euro;';
        $totalXUeInvoice=number_format(  $billingJournal->totalXUeInvoice,2,'.') . ' &euro;';
        $groupUeTextReceipt=$billingJournal->groupUeTextReceipt;
        $groupUeTextInvoice=$billingJournal->groupUeTextInvoice;
        $groupXUeTextInvoice=$billingJournal->groupXUeTextInvoice;
        $dateNow=new\DateTime();
        $datePrint=$dateNow->format('Y-m-d 00:00:00');
        $billingJournal->datePrint=$datePrint;
        $billingJournal->update();
        $sql = "SELECT invoiceText as invoiceText,
                      invoiceDate as invoiceDate,
                      orderId as orderId,
                      concat(invoiceNumber,'/',invoiceType,'/',invoiceYear) as numberInvoice,
                      invoiceType as invoiceType
                      
              FROM Invoice 
                
                WHERE invoiceDate between '" . $dateFilter . " 00:00:00' and '" . $dateFilter . " 23:59:59' and invoiceType='K'";
        $invoiceText='';
        /** @var CRepo $orderRepo */
        $orderRepo=\Monkey::app()->repoFactory->create('Order');
        $orderLineRepo=\Monkey::app()->repoFactory->create('OrderLine');
        $productRepo=\Monkey::app()->repoFactory->create('Product');
        $testolineadimarmo='';
        $orderLineTable='';
        $resultTextInvoice = \Monkey::app()->dbAdapter->query($sql, [])->fetchAll();
        $orderTot=0;
        $orderVatTot=0;

        forEach ($resultTextInvoice as $resultTextInvoices) {
            $stringInvoice=$resultTextInvoices['invoiceText'];
            $invoiceText .= get_string_between($stringInvoice, '<!--start-->', '<!--end-->');
            $invoiceText .='<div class="newpage">';
            $order=$orderRepo->findOneBy(['Id'=>$resultTextInvoices['orderId']]);
            $customerDetail=CUserAddress::defrost($order->frozenBillingAddress);
            $customerName=$customerDetail->name.' '.$customerDetail->surname.' '.$customerDetail->company;
            $orderId=$order->id;
            $orderLine=$orderLineRepo->findBy(['orderId'=>$orderId]);
            $orderTot+=$order->netTotal;
            $orderVatTot+=$order->vat;


$numeropezzi=0;
            foreach ($orderLine as $orderLines) {
                 $productSku = \bamboo\domain\entities\CProductSku::defrost($orderLines->frozenProduct);


                                $iscurrentProductSku=$productSku->productId."-".$productSku->productVariantId;
                $numeropezzi=$numeropezzi+1;


            }
            $testolineadimarmo =$testolineadimarmo.'<tr><td class="text-center small">'.$resultTextInvoices['numberInvoice'].'</td><td class="text-center small">'.$orderId.'</td><td class="text-center small">'.$customerName.'</td><td class="text-center small">'.$numeropezzi.'</td><td class="text-center small">'. number_format($order->netTotal,2,'.') . '&euro;'.'</td></tr>';

        }
        $orderImpTot= $orderTot - $orderVatTot;




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
                    'testolineadimarmo'=>$testolineadimarmo,
                    'logo' => $this->app->cfg()->fetch("miscellaneous", "logo"),
                    'orderImpTot'=>$orderImpTot,
                    'orderVatTot' => $orderVatTot,
                    'orderTot'=>$orderTot,
                    'page' => $this->page,
                    'logo' => $this->app->cfg()->fetch("miscellaneous", "logo"),
                    'fiscalData' => $this->app->cfg()->fetch("miscellaneous", "fiscalData"),

                ]);



            return $renderRegister;
        }


}

