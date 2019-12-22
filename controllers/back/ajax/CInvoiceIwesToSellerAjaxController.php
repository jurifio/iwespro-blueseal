<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;
use DateTime;
use Monkey;
use PDO;
use PDOException;

/**
 * Class CInvoiceIwesToSellerAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/12/2019
 * @since 1.0
 */
class CInvoiceIwesToSellerAjaxController extends AAjaxController
{


    public function post()
    {



        $orderId = $this->app->router->request()->getRequestData('orderId');

        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $userAddressRepo = \Monkey::app()->repoFactory->create('AddressBook');
        $order = $orderRepo->findOneBy(['id' => $orderId]);
        $orderDate = $order->orderDate;
        $isUserBilling[] = json_decode($order->frozenBillingAddress,true);
        $countryFind = $isUserBilling[0]['countryId'];
        $isFindExtraUe = \Monkey::app()->repoFactory->create('Country')->findOneBy(['id' => $countryFind]);
        $isExtraUe = $isFindExtraUe->extraue;
        $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $remoteShopSellerId=$order->remoteShopSellerId;
        $invoiceTypeText = "Fattura Proforma N. :";
        $invoiceHeaderText = "FATTURA";
        $invoiceTotalDocumentText = "Totale Fattura";
        $invoiceType='P';
        if ($remoteShopSellerId != 44) {

            $findShopEcommerce = $shopRepo->findOneBy(['id' => $remoteShopSellerId]);
            if ($findShopEcommerce->hasEcommerce == 1) {
                $db_hostSeller = $findShopEcommerce->dbHost;
                $db_nameSeller = $findShopEcommerce->dbName;
                $db_userSeller = $findShopEcommerce->dbUsername;
                $db_passSeller = $findShopEcommerce->dbPassword;
                $fee=$findShopEcommerce->paralellFee;
                $userAddress=$userAddressRepo->findOneBy(['id'=>$findShopEcommerce->billingAddressBookId]);
                $shippingAddress[] = json_decode($order->frozenShippingAddress, true);

                $findShopHeadInvoice=$shopRepo->findOneBy(['id'=>44]);
                $logo = $findShopHeadInvoice->logo;
                $intestation = $findShopHeadInvoice->intestation;
                $intestation2 =$findShopHeadInvoice->intestation2;
                $address = $findShopHeadInvoice->address;
                $address2 = $findShopHeadInvoice->address2;
                $iva = $findShopHeadInvoice->iva;
                $tel = $findShopHeadInvoice->tel;
                $email = $findShopHeadInvoice->email;


                try {

                    $db_con = new PDO("mysql:host={$db_hostSeller};dbname={$db_nameSeller}",$db_userSeller,$db_passSeller);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                    $res = ' connessione ok <br>';
                } catch (PDOException $e) {
                    $res = $e->getMessage();
                }
                $changelanguage=0;
                $remoteOrderSellerId=$order->remoteOrderSellerId;
                $dateInvoice = strtotime($order->orderDate);
                $invoiceYear=date('Y',$dateInvoice);
                $em = $this->app->entityManagerFactory->create('ShopHasCounter');

                        $number=$em->query("SELECT (shc.invoiceCounter+1) as `new` from ShopHasCounter shc
                                            join Shop s ON shc.shopId=s.id where shc.shopId=? and s.siteInvoiceChar=? and shc.invoiceYear=?", [44,'P',$invoiceYear])->fetchAll()[0]['new'];


                        $invoiceNew = $invoiceRepo->getEmptyEntity();
                $invoiceNew->orderId=$orderId;
                $invoiceNew->invoiceShopId=44;
                $invoiceNew->invoiceNumber = $number;
                $invoiceNew->invoiceSiteChar ='P';
                $invoiceNew->invoiceType = 'P';

                $newdateInvoice = date('Y-m-d H:i:s',$dateInvoice);
                $invoiceNew->invoiceDate=$newdateInvoice;
                $invoiceNew->invoiceYear=$invoiceYear;
                $invoiceDate=date('d/m-Y',$dateInvoice);
                //$invoiceNew -> invoiceDate = $today -> format('Y-m-d H:i:s');
                // $dateForRemote = $today -> format('Y-m-d H:i:s');
                $dateForRemote=$newdateInvoice;

                $invoiceText = '';
                    $invoiceText .= '
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>';
                    $invoiceText .= '<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
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
    <style type="text/css">';


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
                             data-src-retina=' . $logo . ' data-src=' . $logo . ' src=' . $logo . '>
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
                                <strong>' . $invoiceTypeText . '</strong>  ' . $number . "/" . $invoiceType . '<strong> del </strong>' . $invoiceDate . '
                            </div>

                        </div>
                        <br>
                        <div>
                            <div class="pull-left font-montserrat small"><strong>';

                    if ($changelanguage != 1) {
                        $referOrder = 'Rif. ordine N. ';
                    } else {
                        $referOrder = 'Order Reference N:';
                    }
                    $invoiceText .= $referOrder;
                    $invoiceText .= '</strong>';
                    $date = new DateTime($order->orderDate);
                    if ($changelanguage != 1) {
                        $refertOrderIdandDate = '  ' . $remoteOrderSellerId . '-' . $orderId . ' del ' . $invoiceDate;
                    } else {
                        $refertOrderIdandDate = '  ' . $remoteOrderSellerId . '-' . $orderId . ' date ' . $invoiceDate;
                    };
                    $invoiceText .= $refertOrderIdandDate . '</div>
                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>';
                    if ($changelanguage != 1) {
                        $invoiceText .= 'Metodo di pagamento';
                    } else {
                        $invoiceText .= 'Payment Method';
                    }
                    $invoiceText .= '</strong>';
                    $invoiceText .= ' Wallet </div>

                        </div>
                    </div>
                    <div class="pull-right sm-m-t-0">
                        <h2 class="font-montserrat all-caps hint-text"><?php echo $invoiceHeaderText; ?></h2>

                        <div class="col-md-12 col-sm-height sm-padding-20">
                            <p class="small no-margin">';
                    if ($changelanguage != 1) {
                        $invoiceText .= 'Intestata a';
                    } else {
                        $invoiceText .= 'Invoice Address';
                    }
                    $invoiceText .= '</p>';
                    $invoiceText .= '<h5 class="semi-bold m-t-0 no-margin">' . $userAddress->name . ' ' . $userAddress->subject . '</h5>';
                    $invoiceText .= '<address>';
                    $invoiceText .= '<strong>';
                    $invoiceText .= $userAddress->address . ' ' . $userAddress->extra;
                    $invoiceText .= '<br>' . $userAddress->postcode . ' ' . $userAddress->city . ' (' . $userAddress->province . ')';
                    $countryRepo = \Monkey ::app() -> repoFactory -> create('Country') -> findOneBy(['id' => $userAddress->countryId]);
                    $invoiceText .= '<br>' . $countryRepo -> name;
                    if ($changelanguage != 1) {
                        $transfiscalcode = 'C.FISC. o P.IVA: ';
                    } else {
                        $transfiscalcode = 'VAT';
                    }
                    $invoiceText .= '<br>';

                    $invoiceText .= $transfiscalcode . $userAddress -> vatNumber;


                    $invoiceText .= '</strong>';
                    $invoiceText .= '</address>';
                    $invoiceText .= '<div class="clearfix"></div><br><p class="small no-margin">';
                    if ($changelanguage != 1) {
                        $invoiceText .= 'Indirizzo di Spedizione';
                    } else {
                        $invoiceText .= 'Shipping Address';
                    }

                    $invoiceText .= '</p><address>';
                    $invoiceText .= '<strong>' . $shippingAddress[0]['name'] . ' ' . $shippingAddress[0]['name'] . ' ' . $shippingAddress[0]['name'];
                    $invoiceText .= '<br>' . $shippingAddress[0]['address'];
                    $invoiceText .= '<br>' . $shippingAddress[0]['postcode'] . ' ' . $shippingAddress[0]['city'] . ' (' . $shippingAddress[0]['province'] . ')';
                    $countryRepo = \Monkey ::app() -> repoFactory -> create('Country') -> findOneBy(['id' => $shippingAddress[0]['countryId']]);
                    $invoiceText .= '<br>' . $countryRepo -> name . '</strong>';
                    $invoiceText .= '</address>';
                    $invoiceText .= '</div>';
                    $invoiceText .= '</div>';
                    $invoiceText .= '</div>';
                    $invoiceText .= '<table class="table invoice-table m-t-0">';
                    $invoiceText .= '<thead>
                    <!--tabella prodotti-->
                    <tr>';
                    $invoiceText .= '<th class="small">';
                    if ($changelanguage != 1) {
                        $invoiceText .= 'Descrizione Prodotto';
                    } else {
                        $invoiceText .= 'Description';
                    }
                    $invoiceText .= '</th>';
                    $invoiceText .= '<th class="text-center small">';
                    if ($changelanguage != 1) {
                        $invoiceText .= 'Taglia';

                    } else {
                        $invoiceText .= 'Size';
                    }
                    $invoiceText .= '</th>';
                    $invoiceText .= '<th></th>';
                    $invoiceText .= '<th class="text-center small">';
                    if ($changelanguage != 1) {
                        $invoiceText .= 'Importo';
                    } else {
                        $invoiceText .= 'Amount';
                    }
                    $invoiceText .= '</th>';

                    $invoiceText .= '</tr></thead><tbody>';
                    $discount = 0;
                    $shippingCharge = 0;
                    $imponibile = 0;
                    $vat = 0;
                    $tot = 0;
                $orderLine=$orderLineRepo->findBy(['orderId'=>$orderId]);
                    foreach($orderLine as $ol){
                        if ($ol -> status != 'ORD_FRND_CANC') {

                            $invoiceText .= '<tr>';
                            $invoiceText .= '<td class="">';

                            $productSku = \bamboo\domain\entities\CProductSku::defrost($ol->frozenProduct);
                            $productRepo = \Monkey::app()->repoFactory->create('Product');
                            $productNameTranslationRepo=\Monkey::app()->repoFactory->create('ProductNameTranslation');
                            $productNameTranslation = $productNameTranslationRepo->findOneBy(['productId' => $ol->productId,'productVariantId' => $ol->productVariantId,'langId' => '1']);
                            $invoiceText .= (($productNameTranslation) ? $productNameTranslation->name : '') . ($ol->warehouseShelfPosition ? ' / ' . $ol->warehouseShelfPosition->printPosition() : '') . '<br />' . $productSku->product->productBrand->name . ' - ' . $productSku->productId . '-' . $productSku->productVariantId;
                            $invoiceText .= '</td>';
                            $productSize = \Monkey::app()->repoFactory->create('ProductSize')->findOneBy(['id' => $ol->productSizeId]);
                            $invoiceText .= '<td class="text-center">' . $productSize->name;
                            $invoiceText .= '<td></td>';
                            $invoiceText .= '</td>';
                            $invoiceText .= '<td class="text-center">';

                            $tot += $ol->activePrice;
                            $discount += $ol->couponCharge;
                            $tot+=$discount;
                            $shippingCharge += $ol->shippingCharge;
                            $tot+=$shippingCharge;
                            $invoiceText .= money_format('%.2n', $ol->activePrice+$ol->couponCharge+$ol->shippingCharge) . ' &euro;' . '</td></tr>';

                            $feeSeller = ($ol->activePrice+$ol->couponCharge+$ol->shippingCharge) / 100 * $fee;
                            $tot -= $feeSeller;
                            $invoiceText .= '<tr><td colspan="2" class="text-center">Commissione di Vendita</td><td class="text-center">-' . money_format('%.2n', $feeSeller) . ' &euro;' . '</td></tr>';
                        }
                    }

                    $invoiceText .= '</tbody><br><tr class="text-left font-montserrat small">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px">
                            <strong>';
                    if ($changelanguage != 1) {
                        $invoiceText .= 'Totale della Merce';
                    } else {
                        $invoiceText .= 'Total Amount ';
                    }
                    $invoiceText .= '</strong></td>
                        <td style="border: 0px"
                            class="text-center">' . money_format('%.2n', $tot) . ' &euro;' . '</td>
                    </tr>';

                    ($changelanguage != 1) ? $transdiscount = 'Sconto' : $transdiscount = 'Discount';
                    ($changelanguage != 1) ? $transmethodpayment = 'Modifica di pagamento' : $transmethodpayment = 'Transaction Discount';
                    ($changelanguage != 1) ? $transdeliveryprice = 'Spese di Spedizione' : $transdeliveryprice = 'Shipping Cost';
                    $invoiceText .= ((!is_null($discount)) && ($discount != 0)) ? '<tr class="text-left font-montserrat small">
                            <td style="border: 0px"></td>
                            <td style="border: 0px"></td>
                            <td style="border: 0px">' . $transdiscount . '<strong></strong></td>
                            <td style="border: 0px" class="text-center">' . money_format('%.2n', $discount) . ' &euro; </td></tr>' : null;
                    $invoiceText .= ((!is_null($order->paymentModifier)) && ($order->paymentModifier != 0)) ? '<tr class="text-left font-montserrat small">
                            <td style="border: 0px"></td>
                            <td style="border: 0px"></td><td style="border: 0px"><strong>' . $transmethodpayment . '</strong></td>
                            <td style="border: 0px" class="text-center">' . money_format('%.2n', $order->paymentModifier) . ' &euro; </td></tr>' : null;
                    $invoiceText .= '<tr class="text-left font-montserrat small">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td class="separate"><strong>' . $transdeliveryprice . '</strong></td>
                        <td class="separate text-center">0.00 &euro;</td>
                    </tr>
                    <tr style="border: 0px" class="text-left font-montserrat small hint-text">
                        <td class="text-left" width="30%">';


                $invoiceText .= 'Imponibile<br>';

                $imp = ($tot * 100) / 122;
                $invoiceText .= money_format('%.2n', $imp) . ' &euro;';

                $invoiceText .= '<br>';


                $invoiceText .= '</td>
                        <td class="text-left" width="25%">';


                $invoiceText .= 'IVA 22%<br>';

                $iva = ($imp / 100) * 22;
                $invoiceText .= money_format('%.2n', $iva) . ' &euro;';

                $invoiceText .= '<br>';


                $invoiceText .= '<br></td>';
                $invoiceText .= '<td class="semi-bold"><h4>Totale Fattura</h4></td>';
                $invoiceText .= '<td class="semi-bold text-center">
                            <h2>' . money_format('%.2n', $tot) . ' &euro; </h2></td>
                    </tr>
                </table>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div>
                <center><img alt="" class="invoice-thank" data-src-retina="/assets/img/invoicethankyou.jpg"
                             data-src="/assets/img/invoicethankyou.jpg" src="/assets/img/invoicethankyou.jpg">
                </center>
            </div>
            <br>
            <br>
        </div>
    </div>
</div><!--end-->';

                    $invoiceText .= '<script type="application/javascript">
    $(document).ready(function () {

        Pace.on(\'done\', function () {

            setTimeout(function () {
                window.print();

                setTimeout(function () {
                    window.close();
                }, 1);

            }, 200);

        });
    });
</script>
</body>
</html>';
                    $invoiceNew->invoiceText=$invoiceText;
                    $invoiceNew->insert();
                try {
                    $stmtInsertRemoteInvoiceSeller = $db_con -> prepare('INSERT INTO Invoice
    (orderId,invoiceYear,invoiceType,invoiceSiteChar,invoiceNumber,invoiceDate,invoiceText,creationDate,remoteIwesOrderId,remoteOrderSupplierId) 
    VALUES ( 
    \'' . $remoteOrderSellerId . '\',
    \'' . $invoiceYear . '\',
     \'P\',
    \'P\',
    \'' . $number . '\',
    \'' . $dateForRemote . '\',
    \'' . $invoiceText . '\',
    now(),
    \'' . $orderId . '\',
   null)');
                    $stmtInsertRemoteInvoiceSeller -> execute();
                    $res='Inserimento Fattura su Seller Eseguita Correttamente';

                } catch (\Throwable $exception) {
                    $sql = 'INSERT INTO Invoice
    (orderId,invoiceYear,invoiceType,invoiceSiteChar,invoiceNumber,invoiceDate,invoiceText,creationDate,remoteIwesOrderId,remoteOrderSupplierId) 
    VALUES ( 
    \'' . $remoteOrderSellerId . '\',
    \'' . $invoiceYear . '\',
     \'P\',
    \'P\',
    \'' . $number . '\',
    \'' . date('Y-m-d H:i:s') . '\',
    \'' . $invoiceText . '\',
    now(),
    \'' . $orderId . '\',
    null)';

                    \Monkey ::app() -> applicationLog('CInvoiceIwesToSellerAjaxController.php', 'error', 'query remota', $sql, $exception);
                    $res=$exception;

                }


            }

            return $res;
        }else{
            return 'L\'ordine  appartine alla piattaforma Pickyshop';
        }


    }

}

