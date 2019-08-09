<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <style type="text/css">

        li {font-size:8pt;}

        .cover {
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:#c0c0c0;
            z-index:9998;
        }

        .cover div {
            position:absolute;
            top:35%;
            left:40%;
            background:#fff;
            border-radius:5px;
            color:#000;
            z-index:9999;
            padding:100px;
        }

        @media print {
            body{zoom: 100%}

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
                display:none;
            }

            .page-container {
                display:block;
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
<div class="cover"><div>Usa CTRL-P per stampare</div></div>

<?php
$dirtyProductRepo=\Monkey::app()->repoFactory->create('DirtyProduct');
$dirtySKuRepo=\Monkey::app()->repoFactory->create('DirtySku');
foreach ($products as $product):
    /** @var \bamboo\domain\entities\CProduct $product */?>
<div class="container newpage">
    <div class="row"
        <div class="col-md-12">
            <div class="col-md-4" style="margin-top:10px">
                <?php
                $findDirtyProductInt=$dirtyProductRepo->findOneBy(['productId'=>$product->id,'productVariantId'=>$product->productVariantId]);
                $dirtyProductId = $findDirtyProductInt->id;
                $findBarcodeInt=$dirtySKuRepo->findOneBy(['dirtyProductId'=>$dirtyProductId]);
                $barcodeInt=$findBarcodeInt->barcode;
                 if(empty($barcodeInt)): ?>
                <img src="<?php echo $aztecFactoryEndpoint.$product->aztecCode ; ?>" width="140" height="140"/>
                <?php else: ?>
                <img src="<?php echo $aztecFactoryEndpoint.$product->aztecCode.'__'.$barcodeInt ; ?>" width="140" height="140"/>
                <?php endif; ?>
            </div>
            <div class="col-md-4" style="border-right: 1px dotted #c0c0c0;">
                <ul>
                    <li><strong>INT</strong> <?php echo $product->printId(); ?></li>
                    <li><strong>CPF</strong> <?php echo $product->itemno; ?> # <?php echo $product->productVariant->name; ?></li>
                    <li><strong>BRD</strong> <?php echo $product->productBrand->slug; ?></li>
                    <li><strong>SHP</strong> <?php echo $product->getShops() ?></li>
                    <li><strong>SEX</strong> <?php echo implode(', ',$product->getGendersName()) ?></li>
                    <li><strong>DDT</strong> <?php echo $product->getDdt() ?></li>
                    <li><strong>NOTE</strong> <?php echo $temp ?></li>
                </ul>
            </div>
            <div class="col-md-4">
                <ul>
                    <li><strong>BARCODE_INT</strong> <?php echo (!empty($barcodeInt) ? $barcodeInt : "---" ) ?></li>
                    <li><strong>EXTID</strong> <?php echo $product->getShopExtenalIds('<br />') ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<script type="application/javascript">
    $(document).ready(function() {

        Pace.on('done', function() {

            setTimeout(function() {
                window.print();

                setTimeout(function() {
                    window.close();
                },1);

            },200);

        });
    });
</script>
</body>
</html>