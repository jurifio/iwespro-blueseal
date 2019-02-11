

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
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
            <div align="center">Registro Rendiconto del giorno<?php
                echo $date;
                ?> </div>
            <table class="table invoice-table m-t-0">
                <thead>
                <!--tabella prodotti-->
                <tr>
                    <th  class="text-center small">Data </th>
                    <th colspan="2" class="text-center small">Ricevute<br>
                                                  Imponibile<br>
                                                  Iva<br>
                                                  ToTale</th>

                    <th colspan="2" class="text-center small">Fatture UE<br>
                        Imponibile<br>
                        Iva<br>
                        ToTale</th>
                    <th colspan="2" class="text-center small">Fatture ExtraUE<br>
                        Imponibile<br>
                        Iva<br>
                        ToTale</th>

                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center small">Corrispettivo</td>
                    <td class="text-center small">Ricevute<br>
                        Imponibile<br>
                        Iva<br>
                        ToTale</td>
                    <td class="text-center small">Elenco Ricevute</td>
                    <td class="text-center small">Fatture UE<br>
                        Imponibile<br>
                        Iva<br>
                        ToTale</td>
                    <td class="text-center small">Elenco Fatture UE</td>
                    <td class="text-center small">Fatture ExtraUE<br>
                        Imponibile<br>
                        Iva<br>
                        ToTale</td>
                    <td class="text-center small">Elenco Fatture ExtraUE</td>


                </tr>

                <tr>
                    <td class="text-center small"><?php echo $date;?></td>
                    <td class="text-center small">
                        <?php echo $totalUeNetReceipt;?><br>
                        <?php echo $totalUeVatReceipt;?><br>
                        <?php echo $totalUeReceipt;?></td>
                    <td class="text-center small"><?php echo $groupUeTextReceipt;?></td>
                    <td class="text-center small">
                        <?php echo $totalUeNetInvoice;?><br>
                        <?php echo $totalUeVatInvoice;?><br>
                        <?php echo $totalUeInvoice;?></td>
                    <td class="text-center small"><?php echo $groupUeTextInvoice;?></td>
                    <td class="text-center small">
                        <?php echo $totalXUeNetInvoice;?><br>
                        <?php echo $totalXUeVatInvoice;?><br>
                        <?php echo $totalXUeInvoice;?></td></td>
                    <td class="text-center small"><?php echo $groupXUeTextInvoice;?></td>


                </tr>

                </tbody>



            </table>
            <table class="table invoice-table m-t-0">
                <thead>
                <!--tabella prodotti-->
                <tr>
                    <th  class="text-center small">Documento </th>
                    <th  class="text-center small">ordine</th>
                    <th  class="text-center small">Cliente</th>
                    <th  class="text-center small">Prodotto</th>
                    <th  class="text-center small">Quantita'</th>
                    <th  class="text-center small">Importo</th>
                </tr>
                </thead>
                <tbody>
                <?php echo $testolineadimarmo;?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<div class="newpage">
    <?php echo $invoiceText;?>

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