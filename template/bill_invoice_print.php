<?php
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
                                    $invoiceHeaderText='Fattura';

                                    } else {
                                    $invoiceHeaderText='Invoice';

                                    }
                                    $invoiceText .='</div>
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
                        $invoiceText .= 'Sconto';
                        } else {
                        $invoiceText .= 'Discount';
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
                    $invoiceText .= '<td class="text-center">' . money_format('%.2n',$rowInvoice['priceRow']) . ' &euro;' . '</td>';
                    $invoiceText .= '<td class="text-center">' . $rowInvoice['percDiscount'] . '%: ' . money_format('%.2n',$rowInvoice['discountRow']) . ' &euro;' . '</td>';
                    $customerTaxesRow = \Monkey::app()->repoFactory->create('BillRegistryTypeTaxes')->findOneBy(['id' => $rowInvoice['billRegistryTypeTaxesId']]);
                    $invoiceText .= '<td class="text-center">' . $customerTaxesRow->perc . '%: ' . money_format('%.2n',$rowInvoice['vatRow']) . ' &euro;' . '</td>';
                    $invoiceText .= '<td class="text-center">' . money_format('%.2n',$rowInvoice['grossTotalRow']) . ' &euro;' . '</td></tr>';
                }

                }
                $invoiceText .= '</tbody><br><tr class="text-left font-montserrat small">
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
                        class="text-center">' . money_format('%.2n',$netTotal) . ' &euro;' . '</td>
                </tr>';
                $invoiceText .= '<tr class="text-left font-montserrat small">
                    <td style="border: 0px"></td>
                    <td style="border: 0px"></td>
                    <td style="border: 0px">
                        <strong>';
                            if($discountTotal!='0.00') {
                            if ($isExtraUe != 1) {
                            $invoiceText .= 'Sconto Total';
                            } else {
                            $invoiceText .= 'Total Discount ';
                            }
                            $invoiceText .= '</strong></td>
                    <td style="border: 0px"
                        class="text-center">' . money_format('%.2n',$discountTotal) . ' &euro;' . '</td>
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
                    $invoiceText .= '<td style="border: 0px" class="text-center">' . money_format('%.2n',$vatTotal) . ' &euro;' . '</td></tr>';
                } else {
                $invoiceText .= '<td style="border: 0px" class="text-center">' . money_format('%.2n',0) . ' &euro;' . '</td></tr>';
                }

                $invoiceText .= '<tr style="border: 0px" class="text-left font-montserrat small hint-text">
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
                    $invoiceText .= '<td style="border: 0px" class="text-center">' . money_format('%.2n',$grossTotal) . ' &euro;' . '</td></tr>';
                } else {
                $invoiceText .= '<td style="border: 0px" class="text-center">' . money_format('%.2n',$netTotal) . ' &euro;' . '</td></tr>';
                }
                $invoiceText .= '<tr style="border: 0px" class="text-center">
                    <td colspan="2" style="border: 0px">';
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

$invoiceText .= addslashes('<script type="application/javascript">
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
</html>');
echo stripslashes($invoiceText);
?>