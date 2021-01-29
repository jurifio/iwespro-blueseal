<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\domain\repositories\CEmailRepo;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CSelectAddressBookAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/01/2020
 * @since 1.0
 */
class CPlanningWorkComposeInvoiceAjaxController extends AAjaxController
{
    public function post()
    {
         try {
             $planningWorkId = $this->app->router->request()->getRequestData('planningWorkId');
             $planningWorkStatusId = $this->app->router->request()->getRequestData('planningWorkStatusId');
             $planningWorkTypeId = $this->app->router->request()->getRequestData('planningWorkTypeId');
             $billRegistryClientId = $this->app->router->request()->getRequestData('billRegistryClientId');
             $title = $this->app->router->request()->getRequestData('title');
             $request = $this->app->router->request()->getRequestData('request');
             $startDateWork = $this->app->router->request()->getRequestData('start');
             $endDateWork = $this->app->router->request()->getRequestData('end');
             $solution = $this->app->router->request()->getRequestData('solution');
             $cost = $this->app->router->request()->getRequestData('cost');
             $hour = $this->app->router->request()->getRequestData('hour');
             $total = $cost * $hour;

             $percentageStatus = $this->app->router->request()->getRequestData('percentageStatus');
             $notifyEmail = $this->app->router->request()->getRequestData('notifyEmail');

             $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
             $billRegistryClientAccountRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccount');
             $billRegistryClientAccountHasProductRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccountHasProduct');
             $billRegistryClientBillingInfoRepo = \Monkey::app()->repoFactory->create('BillRegistryClientBillingInfo');
             $billRegistryTypeTaxesRepo = \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes');
             $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
             $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
             $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
             $bri = $billRegistryClientRepo->findOneBy(['id' => $billRegistryClientId]);
             $companyName = $bri->companyName;
             $emailAdmin = $bri->emailAdmin;

             $planningWorkType = \Monkey::app()->repoFactory->create('PlanningWorkType')->findOneBy(['id' => $planningWorkTypeId]);
             $subject = str_replace('{planningWorkId}',$planningWorkId,$planningWorkType->subject);
             $planningWorkEvent = \Monkey::app()->repoFactory->create('PlanningWorkEvent')->findOneBy(['planningWorkId' => $planningWorkId,'planningWorkStatusId' => $planningWorkStatusId,'planningWorkTypeId' => $planningWorkTypeId,'isSent' => '0']);


             $billRegistryClient = \Monkey::app()->repoFactory->create('BillRegistryClient')->findOneBy(['id' => $_GET['billRegistryClientId']]);
             $billRegistryClientId = $billRegistryClient->id;
             $billRegistryClientBillingInfo = $billRegistryClientBillingInfoRepo->findOneBy(['billRegistryClientId' => $billRegistryClientId]);
             $billRegistryClientBillingInfoId = $billRegistryClientBillingInfo->id;
             $billRegistryTypePaymentId = $billRegistryClientBillingInfo->billRegistryTypePaymentId;
             $billRegistryTypeTaxesId = $billRegistryClientBillingInfo->billRegistryTypeTaxesId;
             $bankRegistryId = $billRegistryClientBillingInfo->bankRegistryId;
             $billRegistryTypeTaxes = $billRegistryTypeTaxesRepo->findOneBy(['id' => $billRegistryTypeTaxesId]);
             $percVat = $billRegistryTypeTaxes->perc;
             if ($percVat == 0) {
                 $grossTotal = $total;
                 $vat = 0;
                 $netTotal = $total;

             } else {

                 $vat = $total + ($total / 100 * $percVat);
                 $netTotal = $total;
                 $grossTotal = $netTotal + $vat;
             }


             $dateNow = new \DateTime();
             $dateInvoice = strtotime($dateNow);
             $invoiceDate = date('Y-m-d H:i:s',$dateInvoice);
             $invoiceYear = date('Y',$dateInvoice);
             $todayInvoice = date('d-m-Y',$dateInvoice);

             $invoiceNumber = $em->query("SELECT (shc.invoiceCounter+1) as `new` from ShopHasCounter  where shc.shopId=?  and shc.invoiceYear=?",[57,$invoiceYear])->fetchAll()[0]['new'];


             $billRegistryInvoiceInsert = $billRegistryInvoiceRepo->getEmptyEntity();
             $billRegistryInvoiceInsert->invoiceNumber = $invoiceNumber;
             $billRegistryInvoiceInsert->invoiceYear = $invoiceYear;
             $billRegistryInvoiceInsert->invoiceType = 'W';
             $billRegistryInvoiceInsert->invoiceSiteChar = 'W';
             $billRegistryInvoiceInsert->billRegistryClientId = $billRegistryClientId;
             $billRegistryInvoiceInsert->billRegistryTypePaymentId = $billRegistryTypePaymentId;
             $billRegistryInvoiceInsert->billRegistryClientBillingInfoId = $billRegistryClientBillingInfoId;
             $billRegistryInvoiceInsert->netTotal = $netTotal;
             $billRegistryInvoiceInsert->vat = $vat;
             $billRegistryInvoiceInsert->bankRegistryId = $bankRegistryId;
             $billRegistryInvoiceInsert->discountTotal = "0";
             $billRegistryInvoiceInsert->grossTotal = $grossTotal;
             $billRegistryInvoiceInsert->invoiceDate = $invoiceDate;
             $billRegistryInvoiceInsert->statusId = 1;
             $billRegistryInvoiceInsert->subject = 'ticket #' . $planningWorkId;
             $billRegistryInvoiceInsert->insert();
             $res = \Monkey::app()->dbAdapter->query('select max(id) as id from BillRegistryInvoice ',[])->fetchAll();
             foreach ($res as $result) {
                 $lastBillRegistryInvoiceId = $result['id'];
             }

             $billRegistryInvoiceRowRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoiceRow');
             $rowInvoiceDetail = [];
             $billRegistryInvoiceRowInsert = $billRegistryInvoiceRowRepo->getEmptyEntity();
             $billRegistryInvoiceRowInsert->billRegistryInvoiceId = $lastBillRegistryInvoiceId;

             $billRegistryProductId = '0';


             $billRegistryInvoiceRowInsert->billRegistryProductId = $billRegistryProductId;
             $billRegistryInvoiceRowInsert->description = $title;
             $billRegistryInvoiceRowInsert->qty = $hour;
             $billRegistryInvoiceRowInsert->priceRow = $cost;
             $billRegistryInvoiceRowInsert->netPriceRow = $netTotal;
             $billRegistryInvoiceRowInsert->vatRow = $vat;
             $billRegistryInvoiceRowInsert->discountRow = '0';
             $billRegistryInvoiceRowInsert->percentDiscount = '0';
             $billRegistryInvoiceRowInsert->grossTotalRow = $grossTotal;
             $billRegistryInvoiceRowInsert->billRegistryTypeTaxesId = $billRegistryTypeTaxesId;
             $billRegistryInvoiceRowInsert->insert();


             $rowInvoiceDetail[] = [
                 'billRegistryProductId' => $billRegistryProductId,
                 'description' => $title,
                 'qty' => $hour,
                 'priceRow' => $cost,
                 'netPrice' => $netTotal,
                 'vatRow' => $vat,
                 'discountRow' => '0',
                 'percDiscount' => '0',
                 'grossTotalRow' => $grossTotal,
                 'billRegistryTypeTaxesId' => $billRegistryTypeTaxesId,
                 'billRegistryTypeTaxesDesc' => $billRegistryTypeTaxes->description . '<br>' . $billRegistryTypeTaxes->perc,
                 'billRegistryContractId' => '0',
                 'billRegistryContractRowId' => '0',
                 'billRegistryContractRowDetailId' => '0'];


             $billRegistryTypePayment = $billRegistryTypePaymentRepo->findOneBy(['id' => $billRegistryTypePaymentId]);
             $namePayment = $billRegistryTypePayment->name;

             $filterBillRegistryTypePayment = $billRegistryTypePaymentRepo->findBy(['name' => $namePayment]);
             $numberRate = 1;
             foreach ($filterBillRegistryTypePayment as $rowsPayment) {
                 $billRegistryTimeTable = $billRegistryTimeTableRepo->getEmptyEntity();
                 $billRegistryTimeTable->typeDocument = '7';
                 $billRegistryTimeTable->billRegistryTypePaymentId = $billRegistryTypePaymentId;

                 $billRegistryTimeTable->billRegistryInvoiceId = $lastBillRegistryInvoiceId;
                 $amountRate = $grossTotal / 100 * $rowsPayment->prc;
                 $dateNow = new \DateTime($invoiceDate);
                 if ($rowsPayment->day == '0') {

                     $modPayment = $dateNow->modify('+ ' . $rowsPayment->numDay . ' day');
                     $isWeekEnd = $dateNow->format('D');

                     if ($isWeekEnd == 'Sat' || $isWeekEnd == 'Sun') {
                         $modPayment = $dateNow->modify('+ 2 day');
                     }
                     $estimatedPayment = $modPayment->format('d-m-Y');
                     $dbEstimatedPayment = $modPayment->format('Y-m-d H:i:s');


                 } elseif ($rowsPayment->day == '15') {
                     if ($day15 <= 16) {
                         $modPayment = $dateNow->modify('+ ' . $rowsPayment->numDay . 'day');
                         $day15 = $modPayment->format('d');
                         $month15 = $modPayment->format('m');
                         $year15 = $modPayment->format('Y');
                         $estimatedPayment = '15' . '-' . $mont15 . '-' . $year15;
                         $dbEstimatedPaymentTemp = $year15 . '-' . $month15 . '-' . $day15;
                         $dbEstimatedPaymentTemp2 = new \DateTime($dbEstimatedPaymentTemp);
                         $dbEstimatedPayment = $dbEstimatedPaymentTemp2->format('Y-m-d H:i:s');
                     } else {
                         $modPayment = $dateNow->modify('+ 60 day');

                         $day15 = $modPayment->format('d');
                         $month15 = $modPayment->format('m');
                         $year15 = $modPayment->format('Y');
                         $isWeekEnd = $modPayment->format('D');
                         if ($isWeekEnd == 'Sat' || $isWeekEnd == 'Sun') {
                             $estimatedPayment = '18' . '-' . $mont15 . '-' . $year15;
                             $dbEstimatedPaymentTemp = $year15 . '-' . $month15 . '-18';
                         } else {
                             $estimatedPayment = '15' . '-' . $mont15 . '-' . $year15;
                             $dbEstimatedPaymentTemp = $year15 . '-' . $month15 . '-' . $day15;
                         }

                         $dbEstimatedPaymentTemp2 = new \DateTime($dbEstimatedPaymentTemp);
                         $dbEstimatedPayment = $dbEstimatedPaymentTemp2->format('Y-m-d H:i:s');
                     }


                 } elseif ($rowsPayment->day == '-1') {
                     $modPayment = $dateNow->modify('+ ' . $rowsPayment->numDay . 'day');
                     $day30 = $modPayment->format('d');
                     $month30 = $modPayment->format('m');
                     $year30 = $modPayment->format('Y');
                     $isWeekEnd = $modPayment->format('D');
                     if ($isWeekEnd == 'Sat' || $isWeekEnd == 'Sun') {
                         $estimatedPayment = '28' . '-' . $mont30 . '-' . $year30;
                         $dbEstimatedPaymentTemp = $year30 . '-' . $month30 . '-28';
                     } else {
                         $estimatedPayment = '30' . '-' . $mont30 . '-' . $year30;
                         $dbEstimatedPaymentTemp = $year30 . '-' . $month30 . '-' . $day30;
                     }

                     $dbEstimatedPaymentTemp2 = new \DateTime($dbEstimatedPaymentTemp);
                     $dbEstimatedPayment = $dbEstimatedPaymentTemp2->format('Y-m-d H:i:s');

                 }
                 $billRegistryTimeTable->description = money_format('%.2n',$amountRate) . '  &euro; da corrisponedere entro il ' . $estimatedPayment;
                 $billRegistryTimeTable->dateEstimated = $dbEstimatedPayment;
                 $billRegistryTimeTable->amountPayment = number_format($amountRate,2,'.','');
                 $billRegistryTimeTable->billRegistryClientId = $billRegistryClientId;
                 $billRegistryTimeTable->insert();
             }


             $billRegistryClient = $billRegistryClientRepo->findOneBy(['id' => $billRegistryClientId]);
             $country = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $billRegistryClient->countryId]);
             $isExtraUe = $country->extraue;
             $shopHub = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id' => 57]);
             $intestation = $shopHub->intestation;
             $intestation2 = $shopHub->intestation2;
             $address = $shopHub->address;
             $address2 = $shopHub->address2;
             $iva = $shopHub->iva;
             $tel = $shopHub->tel;
             $email = $shopHub->email;
             $logoSite = $shopHub->logoSite;
             $logoThankYou = $shopHub->logoThankYou;
             $invoiceType = 'W';

             $invoiceText = '';
             $invoiceText .= addslashes('
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>');
             $invoiceText .= addslashes('<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<link rel="icon" type="image/x-icon" sizes="32x32" href="/assets/img/favicon32.png"/>
<link rel="icon" type="image/x-icon" sizes="256x256" href="/assets/img/favicon256.png"/>
<link rel="icon" type="image/x-icon" sizes="16x16" href="/assets/img/favicon16.png"/>
<link rel="apple-touch-icon" type="image/x-icon" sizes="256x256" href="/assets/img/favicon256.png"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta content="" name="description"/>
<meta content="" name="author"/>
<script>
    paceOptions = {
        ajax: {ignoreURLs: [\'/blueseal/xhr/TemplateFetchController\', \'/blueseal/xhr/CheckPermission\']}
    }
</script>
    <link type="text/css" href="https://www.iwes.pro/assets/css/pace.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://code.jquery.com/ui/1.11.4/themes/flick/jquery-ui.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://s3-eu-west-1.amazonaws.com/bamboo-css/jquery.scrollbar.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://s3-eu-west-1.amazonaws.com/bamboo-css/bootstrap-colorpicker.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://github.com/mar10/fancytree/blob/master/dist/skin-common.less" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.24.0/skin-bootstrap/ui.fancytree.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/basic.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/ui.dynatree.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.6.16/summernote.min.css" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700,300" rel="stylesheet" media="screen"/>
<link type="text/css" href="https://raw.githubusercontent.com/kleinejan/titatoggle/master/dist/titatoggle-dist-min.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/pages-icons.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/pages.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/style.css" rel="stylesheet" media="screen,print"/>
<link type="text/css" href="https://www.iwes.pro/assets/css/fullcalendar.css" rel="stylesheet" media="screen,print"/>
<script  type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
<script  type="application/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script  type="application/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script  type="application/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/pages.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/blueseal.prototype.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/blueseal.ui.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script defer type="application/javascript" src="https://cdn.jsdelivr.net/jquery.bez/1.0.11/jquery.bez.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/unveil/1.3.0/jquery.unveil.min.js"></script>
<script defer type="application/javascript" src="https://s3-eu-west-1.amazonaws.com/bamboo-js/jquery.scrollbar.min.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/Sortable.min.js"></script>
<script defer type="application/javascript" src="https://s3-eu-west-1.amazonaws.com/bamboo-js/bootstrap-colorpicker.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.24.0/jquery.fancytree-all.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone-amd-module.min.js"></script>
<script defer type="application/javascript" src="https://cdn.jsdelivr.net/jquery.dynatree/1.2.4/jquery.dynatree.min.js"></script>
<script defer type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.6.16/summernote.min.js"></script>
<script defer type="application/javascript" src="https://s3-eu-west-1.amazonaws.com/bamboo-js/summernote-it-IT.js"></script>
<script defer type="application/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js"></script>
<script defer type="application/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/additional-methods.min.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/blueseal.kickstart.js"></script>
<script  type="application/javascript" src="https://www.iwes.pro/assets/js/monkeyUtil.js"></script>
<script defer type="application/javascript" src="https://www.iwes.pro/assets/js/invoice_print.js"></script>
<script defer async type="application/javascript" src="https://www.iwes.pro/assets/js/blueseal.common.js"></script>
    <title>BlueSeal - Stampa fattura</title>
    <style type="text/css">');


             $invoiceText .= '
        @page {
            size: A4;
            margin: 5mm 0mm 0mm 0mm;
        }

        @media print {
            body {
                zoom: 100%;
                width: 800px;
                height: 1100px;
                overflow: hidden;
            }

            .container {
                width: 100%;
            }

            .newpage {
                page-break-before: always;
                page-break-after: always;
                page-break-inside: avoid;
            }

            @page {
                size: A4;
                margin: 5mm 0mm 0mm 0mm;
            }

            .cover {
                display: none;
            }

            .page-container {
                display: block;
            }

            /*remove chrome links*/
            a[href]:after {
                content: none !important;
            }

            .col-md-1,
            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-5,
            .col-md-6,
            .col-md-7,
            .col-md-8,
            .col-md-9,
            .col-md-10,
            .col-md-11,
            .col-md-12 {
                float: left;
            }

            .col-md-12 {
                width: 100%;
            }

            .col-md-11 {
                width: 91.66666666666666%;
            }

            .col-md-10 {
                width: 83.33333333333334%;
            }

            .col-md-9 {
                width: 75%;
            }

            .col-md-8 {
                width: 66.66666666666666%;
            }

            .col-md-7 {
                width: 58.333333333333336%;
            }

            .col-md-6 {
                width: 50%;
            }

            .col-md-5 {
                width: 41.66666666666667%;
            }

            .col-md-4 {
                width: 33.33333333333333%;
            }

            .col-md-3 {
                width: 25%;
            }

            .col-md-2 {
                width: 16.666666666666664%;
            }

            .col-md-1 {
                width: 8.333333333333332%;
            }

            .col-md-pull-12 {
                right: 100%;
            }

            .col-md-pull-11 {
                right: 91.66666666666666%;
            }

            .col-md-pull-10 {
                right: 83.33333333333334%;
            }

            .col-md-pull-9 {
                right: 75%;
            }

            .col-md-pull-8 {
                right: 66.66666666666666%;
            }

            .col-md-pull-7 {
                right: 58.333333333333336%;
            }

            .col-md-pull-6 {
                right: 50%;
            }

            .col-md-pull-5 {
                right: 41.66666666666667%;
            }

            .col-md-pull-4 {
                right: 33.33333333333333%;
            }

            .col-md-pull-3 {
                right: 25%;
            }

            .col-md-pull-2 {
                right: 16.666666666666664%;
            }

            .col-md-pull-1 {
                right: 8.333333333333332%;
            }

            .col-md-pull-0 {
                right: 0;
            }

            .col-md-push-12 {
                left: 100%;
            }

            .col-md-push-11 {
                left: 91.66666666666666%;
            }

            .col-md-push-10 {
                left: 83.33333333333334%;
            }

            .col-md-push-9 {
                left: 75%;
            }

            .col-md-push-8 {
                left: 66.66666666666666%;
            }

            .col-md-push-7 {
                left: 58.333333333333336%;
            }

            .col-md-push-6 {
                left: 50%;
            }

            .col-md-push-5 {
                left: 41.66666666666667%;
            }

            .col-md-push-4 {
                left: 33.33333333333333%;
            }

            .col-md-push-3 {
                left: 25%;
            }

            .col-md-push-2 {
                left: 16.666666666666664%;
            }

            .col-md-push-1 {
                left: 8.333333333333332%;
            }

            .col-md-push-0 {
                left: 0;
            }

            .col-md-offset-12 {
                margin-left: 100%;
            }

            .col-md-offset-11 {
                margin-left: 91.66666666666666%;
            }

            .col-md-offset-10 {
                margin-left: 83.33333333333334%;
            }

            .col-md-offset-9 {
                margin-left: 75%;
            }

            .col-md-offset-8 {
                margin-left: 66.66666666666666%;
            }

            .col-md-offset-7 {
                margin-left: 58.333333333333336%;
            }

            .col-md-offset-6 {
                margin-left: 50%;
            }

            .col-md-offset-5 {
                margin-left: 41.66666666666667%;
            }

            .col-md-offset-4 {
                margin-left: 33.33333333333333%;
            }

            .col-md-offset-3 {
                margin-left: 25%;
            }

            .col-md-offset-2 {
                margin-left: 16.666666666666664%;
            }

            .col-md-offset-1 {
                margin-left: 8.333333333333332%;
            }

            .col-md-offset-0 {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="fixed-header">

<!--start-->
<div class="container container-fixed-lg">

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="invoice padding-50 sm-padding-10">
                <div>
                    <div class="pull-left">
                        <!--logo negozio-->
                        <img width="235" height="47" alt="" class="invoice-logo"
                             data-src-retina=' . '"/assets/img/' . $logoSite . '" data-src=' . '"/assets/img/' . $logoSite . '" src=' . '"/assets/img/' . $logoSite . '">
                        <!--indirizzo negozio-->
                        <br><br>
                        <address class="m-t-10"><b>' . $intestation . '
                                <br>' . $intestation2 . '</b>
                            <br>' . $address . '
                            <br>' . $address2 . '
                            <br>' . $iva . '
                            <br>' . $tel . '
                            <br>' . $email . '
                        </address>
                        <br>
                        <div>
                            <div class="pull-left font-montserrat all-caps small">
                                 <strong>Fattura</strong>  ' . $invoiceNumber . "/" . $invoiceType . '<strong> del </strong>' . $todayInvoice . '
                            </div>
                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>';


             if ($isExtraUe != '1') {
                 $invoiceHeaderText = 'Fattura';

             } else {
                 $invoiceHeaderText = 'Invoice';

             }
             $invoiceText .= '</div>
                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>';

             $invoiceText .= '</strong>
                           </div>
                        </div>
                    </div>
                    <div class="pull-right sm-m-t-0">
                        <h2 class="font-montserrat all-caps hint-text">' . $invoiceHeaderText . '</h2>

                        <div class="col-md-12 col-sm-height sm-padding-20">
                            <p class="small no-margin">';
             if ($isExtraUe != '1') {
                 $invoiceText .= 'Intestata a';
             } else {
                 $invoiceText .= 'Invoice Address';
             }
             $invoiceText .= '</p>';

             $invoiceText .= '<h5 class="semi-bold m-t-0 no-margin">' . addslashes($billRegistryClient->companyName) . '</h5>';
             $invoiceText .= '<address>';
             $invoiceText .= '<strong>';


             $invoiceText .= addslashes($billRegistryClient->address . ' ' . $billRegistryClient->extra);
             $invoiceText .= '<br>' . addslashes($billRegistryClient->zipcode . ' ' . $billRegistryClient->city . ' (' . $billRegistryClient->province . ')');
             $countryRepo = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $billRegistryClient->countryId]);
             $invoiceText .= '<br>' . $countryRepo->name;
             if ($isExtraUe != '1') {
                 $transfiscalcode = 'C.FISC. o P.IVA: ';
             } else {
                 $transfiscalcode = 'VAT: ';
             }
             $invoiceText .= '<br>';

             $invoiceText .= $transfiscalcode . $billRegistryClient->vatNumber;


             $invoiceText .= '</strong>';
             $invoiceText .= '</address>';
             $invoiceText .= '<div class="clearfix"></div><br><p class="small no-margin">';


             $invoiceText .= '</p><address>';

             $invoiceText .= '<strong>';

             $invoiceText .= '<br>';
             $invoiceText .= '<br>';

             $invoiceText .= '<br>';
             $invoiceText .= '</address>';
             $invoiceText .= '</div>';
             $invoiceText .= '</div>';
             $invoiceText .= '</div>';
             $invoiceText .= '</div>';
             $invoiceText .= '<table class="table invoice-table m-t-0">';
             $invoiceText .= '<thead>
                    <!--tabella prodotti-->
                    <tr>';
             $invoiceText .= '<th class="small">';
             if ($isExtraUe != "1") {
                 $invoiceText .= 'Descrizione Prodotto';
             } else {
                 $invoiceText .= 'Description';
             }
             $invoiceText .= '</th>';

             $invoiceText .= '<th class="text-center small">';
             if ($isExtraUe != "1") {
                 $invoiceText .= 'Importo';
             } else {
                 $invoiceText .= 'Amount';
             }
             $invoiceText .= '</th>';
             $invoiceText .= '<th class="text-center small">';
             if ($isExtraUe != "1") {
                 $invoiceText .= 'Sconto';
             } else {
                 $invoiceText .= 'Discount';
             }
             $invoiceText .= '</th>';
             $invoiceText .= '<th class="text-center small">';
             if ($isExtraUe != "1") {
                 $invoiceText .= 'iva';
             } else {
                 $invoiceText .= 'vat Total Row';
             }
             $invoiceText .= '<th class="text-center small">';
             if ($isExtraUe != "1") {
                 $invoiceText .= 'Importo Totale';
             } else {
                 $invoiceText .= 'Tot Row';
             }
             $invoiceText .= '</th>';


             $invoiceText .= '</th>';

             $invoiceText .= '</tr></thead><tbody>';

             if ($rowInvoiceDetail != null) {
                 foreach ($rowInvoiceDetail as $rowInvoice) {
                     $invoiceText .= '<tr><td class="text-center">' . $rowInvoice['description'] . '</td>';
                     $invoiceText .= '<td class="text-center">' . number_format($rowInvoice['priceRow'],2,',','.') . ' &euro;' . '</td>';
                     $invoiceText .= '<td class="text-center">sconto' . $rowInvoice['percDiscount'] . ' %: ' . number_format($rowInvoice['discountRow'],2,',','.') . ' &euro;' . '</td>';
                     $customerTaxesRow = \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $rowInvoice['billRegistryTypeTaxesId']]);
                     $invoiceText .= '<td class="text-center">' . $customerTaxesRow->perc . '%: ' . number_format($rowInvoice['vatRow'],2,',','.') . ' &euro;' . '</td>';
                     $invoiceText .= '<td class="text-center">' . number_format($rowInvoice['grossTotalRow'],2,',','.') . ' &euro;' . '</td></tr>';
                 }

             }
             $invoiceText .= '</tbody><br><tr class="text-left font-montserrat small">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px">
                            <strong>';
             if ($isExtraUe != 1) {
                 $invoiceText .= 'Totale Netto';
             } else {
                 $invoiceText .= 'Total Amount ';
             }
             $invoiceText .= '</strong></td>
                        <td style="border: 0px"
                            class="text-center">' . number_format($netTotal,2,',','.') . ' &euro;' . '</td>
                    </tr>';
             $invoiceText .= '</tbody><br><tr class="text-left font-montserrat small">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px">
                            <strong>';
             if ($discountTotal != '0.00') {
                 if ($isExtraUe != 1) {
                     $invoiceText .= 'Sconto Total';
                 } else {
                     $invoiceText .= 'Total Discount ';
                 }
                 $invoiceText .= '</strong></td>
                        <td style="border: 0px"
                            class="text-center">' . number_format($discountTotal,2,',','.') . ' &euro;' . '</td>
                    </tr>';
             }

             $invoiceText .= '<tr style="border: 0px" class="text-left font-montserrat small hint-text">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px">
                            <strong>';
             if ($isExtraUe != "1") {
                 $invoiceText .= 'Totale Tasse';
             } else {
                 $invoiceText .= 'Total Taxes non imponibile ex art 8/A  D.P.R. n. 633/72 ';
             }
             $invoiceText .= '</strong></td>';
             if ($isExtraUe != 1) {
                 $invoiceText .= '<td style="border: 0px" class="text-center">' . number_format($vatTotal,2,',','.') . ' &euro;' . '</td></tr>';
             } else {
                 $invoiceText .= '<td style="border: 0px" class="text-center">' . '0,00  &euro;' . '</td></tr>';
             }

             $invoiceText .= '<tr style="border: 0px" class="text-left font-montserrat small hint-text">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px">
                            <strong>';
             if ($isExtraUe != "1") {
                 $invoiceText .= 'Totale Dovuto';
             } else {
                 $invoiceText .= 'Total Invoice';
             }
             $invoiceText .= '</strong></td>';
             if ($isExtraUe != "1") {
                 $invoiceText .= '<td style="border: 0px" class="text-center">' . number_format($grossTotal,2,',','.') . ' &euro;' . '</td></tr>';
             } else {
                 $invoiceText .= '<td style="border: 0px" class="text-center">' . number_format($netTotal,2,',','.') . ' &euro;' . '</td></tr>';
             }
             $invoiceText .= '<tr style="border: 0px" class="text-center">
                        <td colspan="3" style="border: 0px">';
             if ($isExtraUe != "1") {
                 $invoiceText .= 'Modalità di pagamento ' . $namePayment;
             } else {
                 $invoiceText .= 'Type Payment';
             }

             $invoiceText .= '</td>';
             $invoiceText .= ' <td style="border: 0px"></td>';
             $invoiceText .= ' <td style="border: 0px"></td></tr>';
             $billRegistryTimeTableFind = $billRegistryTimeTableRepo->findBy(['billRegistryInvoiceId' => $lastBillRegistryInvoiceId]);
             foreach ($billRegistryTimeTableFind as $paymentInvoice) {
                 $invoiceText .= '<tr style="border: 0px" class="text-center">
                        <td colspan="2" style="border: 0px">';
                 $invoiceText .= $paymentInvoice->description;
                 $invoiceText .= '</td>';
                 $invoiceText .= ' <td style="border: 0px"></td>';
                 $invoiceText .= ' <td style="border: 0px"></td></tr>';
             }
             $invoiceText .= '</table>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div>
              <center><img alt="" class="invoice-thank" data-src-retina="/assets/img/' . $logoThankYou . '"
                             data-src="/assets/img/' . $logoThankYou . '" src="/assets/img/' . $logoThankYou . '">
                </center>
            </div>
            <br>
            <br>
        </div>
    </div>
</div><!--end-->';

             $invoiceText .= '
</body>
</html>';
             $updateBillRegistryInvoice = \Monkey::app()->repoFactory->create('BillRegistryInvoice')->findOneBy(['id' => $lastBillRegistryInvoiceId]);
             $updateBillRegistryInvoice->invoiceText = stripslashes($invoiceText);
             $updateBillRegistryInvoice->update();
             $shopHasCounter = \Monkey::app()->repoFactory->create('ShopHasCounter')->findOneBy(['shopId' => 57,'invoiceYear' => $invoiceYear]);
             if ($isExtraUe != "1") {
                 $shopHasCounter->invoiceCounter = $invoiceNumber;
             } else {
                 $shopHasCounter->invoiceextraUeCounter = $invoiceNumber;
             }
             $shopHasCounter->update();


             return $lastBillRegistryInvoiceId;
         }catch(\Throwable $e){
             return $e;
         }
    }


}