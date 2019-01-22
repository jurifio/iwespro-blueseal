<?php
$invoiceDate = new DateTime($invoice->invoiceDate);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <style type="text/css">


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


<div class="container container-fixed-lg">

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="invoice padding-50 sm-padding-10">
                <div>
                    <div class="pull-left">
                        <!--logo negozio-->
                        <img width="235" height="47" alt="" class="invoice-logo"
                             data-src-retina=<?php echo $logo ?> data-src=<?php echo $logo ?> src=<?php echo $logo ?>>
                        <!--indirizzo negozio-->
                        <br><br>
                        <address class="m-t-10"><b><?php echo $fiscalData['intestation'] ?>
                                <br><?php echo $fiscalData['intestation2'] ?></b>
                            <br><?php echo $fiscalData['address'] ?>
                            <br><?php echo $fiscalData['address2'] ?>
                            <br><?php echo $fiscalData['iva'] ?>
                            <br><?php echo $fiscalData['tel'] ?>
                            <br><?php echo $fiscalData['email'] ?>
                        </address>
                        <br>
                        <div>
                            <div class="pull-left font-montserrat all-caps small"><strong><?php echo $invoiceTypeText;?></strong>
                                <?php echo '  ' . $invoice->invoiceNumber . '<strong> del </strong>' . $invoiceDate->format('d-m-Y'); ?>
                            </div>

                        </div>
                        <br>
                        <div>
                            <div class="pull-left font-montserrat small"><strong>Rif. ordine N. :</strong>
                                <?php $date = new DateTime($order->orderDate);
                                echo '  ' . $invoice->orderId . ' del ' . $date->format('d-m-Y'); ?></div>

                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>Metodo di pagamento :</strong>
                                <?php echo '  ' . $order->orderPaymentMethod->name; ?></div>

                        </div>
                    </div>
                    <div class="pull-right sm-m-t-0">
                        <h2 class="font-montserrat all-caps hint-text"><?php echo $invoiceHeaderText;?></h2>

                        <div class="col-md-12 col-sm-height sm-padding-20">
                            <p class="small no-margin">Intestata a</p>
                            <h5 class="semi-bold m-t-0 no-margin"><?php echo $userAddress->surname . ' ' . $userAddress->name; ?></h5>
                            <address>
                                <strong><?php echo (!empty($userAddress->company)) ? $userAddress->company . '<br>' : null; ?>
                                    <?php echo $userAddress->address; ?>
                                    <br><?php echo $userAddress->postcode . ' ' . $userAddress->city . ' (' . $userAddress->province . ')'; ?>
                                    <br><?php echo $userAddress->country->name; ?>
                                    <br><?php (!is_null($order->user->userDetails->fiscalCode)) ? 'C.FISC. o P.IVA: ' . $order->user->userDetails->fiscalCode : null; ?>
                                </strong>
                            </address>
                            <div class="clearfix"></div>
                            <br>
                            <p class="small no-margin">Indirizzo di spedizione</p>
                            <address>
                                <strong><?php echo $userShipping->surname . ' ' . $userShipping->name; ?>
                                    <?php echo (!empty($userShipping->company)) ? '<br>' . $userShipping->company : null; ?>
                                    <br><?php echo $userShipping->address; ?>
                                    <br><?php echo $userShipping->postcode . ' ' . $userShipping->city . ' (' . $userShipping->province . ')'; ?>
                                    <br><?php echo $userShipping->country->name; ?></strong>
                            </address>
                        </div>

                    </div>
                </div>
                <table class="table invoice-table m-t-0">
                    <thead>
                    <!--tabella prodotti-->
                    <tr>
                        <th class="small">Descrizione Prodotto</th>
                        <th class="text-center small">Taglia</th>
                        <th></th>
                        <th class="text-center small">Importo</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $tot = 0;
                    foreach ($order->orderLine as $orderLine) { ?>
                        <tr>
                            <td class="">

                                <?php $productSku = \bamboo\domain\entities\CProductSku::defrost($orderLine->frozenProduct);

                                $productNameTranslation = $productRepo->findOneBy(['productId' => $productSku->productId, 'productVariantId' => $productSku->productVariantId, 'langId' => '1']);
                                echo (($productNameTranslation) ? $productNameTranslation->name : '') . ($orderLine->warehouseShelfPosition ? ' / '.$orderLine->warehouseShelfPosition->printPosition() : '').'<br />' . $productSku->product->productBrand->name . ' - ' . $productSku->productId . '-' . $productSku->productVariantId; ?>
                            </td>
                            <td class="text-center"><?php echo $productSku->getPublicSize()->name; ?>
                            <td></td>
                            </td>
                            <td class="text-center"><?php
                                $tot += $orderLine->activePrice;
                                echo money_format('%.2n', $orderLine->activePrice) . ' &euro;'; ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    <br>
                    <tr class="text-left font-montserrat small">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td style="border: 0px"><strong>Totale della merce</strong></td>
                        <td style="border: 0px"
                            class="text-center"><?php echo money_format('%.2n', $tot) . ' &euro;'; ?></td>
                    </tr>
                    <?php $discount = $order->couponDiscount + $order->userDiscount;
                    echo ((!is_null($discount)) && ($discount != 0)) ? '<tr class="text-left font-montserrat small">
                            <td style="border: 0px"></td>
                            <td style="border: 0px"></td>
                            <td style="border: 0px"><strong>Sconto</strong></td>
                            <td style="border: 0px" class="text-center">' . money_format('%.2n', $discount) . ' &euro; </td></tr>' : null; ?>
                    <?php echo ((!is_null($order->paymentModifier)) && ($order->paymentModifier != 0)) ? '<tr class="text-left font-montserrat small">
                            <td style="border: 0px"></td>
                            <td style="border: 0px"></td><td style="border: 0px"><strong>Modifica di pagamento</strong></td>
                            <td style="border: 0px" class="text-center">' . money_format('%.2n', $order->paymentModifier) . ' &euro; </td></tr>' : null; ?>
                    <tr class="text-left font-montserrat small">
                        <td style="border: 0px"></td>
                        <td style="border: 0px"></td>
                        <td class="separate"><strong>Spese di spedizione</strong></td>
                        <td class="separate text-center"><?php echo money_format('%.2n', $order->shippingPrice) . ' &euro;'; ?></td>
                    </tr>
                    <tr style="border: 0px" class="text-left font-montserrat small hint-text">
                        <td class="text-left" width="30%"><?php if ($invoiceType=='F'){ echo "Imponibile";}?><br><?php $imp = ($order->netTotal * 100) / 122;
                            if ($invoiceType=='F'){ echo money_format('%.2n', $imp) . ' &euro;';} ?></td>
                        <td class="text-left" width="25%"><?php if ($invoiceType=='F'){ echo "IVA 22%";}?><br><?php $iva = $order->netTotal - $imp;
                             if ($invoiceType=='F'){echo money_format('%.2n', $iva) . ' &euro;';} ?></td>
                        <td class="semi-bold"><h4><?php echo $invoiceTotalDocumentText;?></h4></td>
                        <td class="semi-bold text-center">
                            <h2><?php echo money_format('%.2n', $order->netTotal) . ' &euro;'; ?></h2></td>
                    </tr>

                </table>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div>
                <center><img alt="" class="invoice-thank" data-src-retina='/assets/img/invoicethankyou.jpg'
                             data-src='/assets/img/invoicethankyou.jpg' src='/assets/img/invoicethankyou.jpg'>
                </center>
            </div>
            <br>
            <br>
        </div>
    </div>
</div>
<script type="application/javascript">
    $(document).ready(function () {

        Pace.on('done', function () {

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
</html>