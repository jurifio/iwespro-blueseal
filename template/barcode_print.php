<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
    <style type="text/css">

        span {
            font-size: 8pt;
        }

        .coderow {
            height: 38.8mm;
            margin-top:0;
            margin-bottom:0;
        }

        .cover {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #c0c0c0;
            z-index: 9998;
        }

        .cover div {
            position: absolute;
            top: 35%;
            left: 40%;
            background: #fff;
            border-radius: 5px;
            color: #000;
            z-index: 9999;
            padding: 100px;
        }

        @page {
            size: A4;
            margin: 20mm 0mm 0mm 14mm;
        }

        @media print {
            body {zoom: 100%}
            .newpage {
                page-break-before: always;
                page-break-after: always;
                page-break-inside: avoid;
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
<div class="cover">
    <div>Usa CTRL-P per stampare</div>
</div>

<?php $i = 0;
$count = $productSkus->count();
foreach ($productSkus as $productSku): ?>

    <?php if($i == 0): ?>
        <div class="container-fluid container-fixed-lg bg-white">
        <div class="row coderow">
    <?php elseif($i % 21 == 0): ?>
        </div>
        <div class="container-fluid container-fixed-lg bg-white newpage">
            <div class="row coderow">
    <?php elseif($i % 3 == 0): ?>
        </div>
        <div class="row coderow">
    <?php endif; ?>
    <div class="col-xs-4" style="border-right: 1px dotted #c0c0c0;">
        <div class="row">
            <div class="col-xs-12" style="margin-top:0px">
                <span style="position:relative;margin-left:27%;font-size: 8pt"><?php echo $productSku->product->printId(); ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12" style="margin-top:0px">
                <?php echo $barcodeGenerator->getBarcode($productSku->barcode, \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128, 2,45); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12" style="margin-top:-7px">
                <span style="position:relative;margin-left:27%;font-size: 8pt"><?php echo $productSku->barcode; ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12" style="">
                <div class="row">
                    <div class="col-md-8">
                        <span><?php echo $productSku->product->itemno . ' # ' . $productSku->product->productVariant->name; ?></span>
                    </div>
                    <div class="col-md-4">
                        <span><?php echo $productSku->productSize->name ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <span style="font-size: 8pt;display:inline-block"><?php echo $productSku->product->productBrand->name ?></span>
                    </div>
                    <div class="col-md-4">
                        <span style="font-size: 10pt"><?php echo $productSku->shopHasProduct->price ?> €</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $i++; endforeach; ?>
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