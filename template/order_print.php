<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">

<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <style type="text/css">

        @page {
            size:landscape;
            margin:5mm 0mm 0mm 0mm;
        }

        @media all {
            .page-break	{ display: none; }
        }

        @media print {
            .page-break	{ display: block; page-break-before: always; }


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

        }
    </style>
</head>
<body class="fixed-header   ">
<div class="container-fluid container-fixed-lg">

    <div class="panel panel-default">

            <div class="invoice padding-20 sm-padding-10">
                    <div class="pull-left">
                        <address><b><?php echo $fiscalData['intestation'] ?>
                            <br><?php echo $fiscalData['intestation2'] ?></b>
                            <br><?php echo $fiscalData['address'] ?>
                            <br><?php echo $fiscalData['address2'] ?>
                            <br><?php echo $fiscalData['iva'] ?>
                            <br><?php echo $fiscalData['tel'] ?>
                            <br><?php echo $fiscalData['email'] ?>
                        </address>
                        <br>
                        <div>
                            <div class="pull-left font-montserrat small" ><strong>Ordine N. :</strong>
                                <?php $date = new DateTime($order->orderDate);
                                echo ' ' . $order->id . ' del ' . $date->format('d-m-Y'); ?>
                            </div>
                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>Metodo di pagamento :</strong>
                                <?php echo '  '. $order->orderPaymentMethod->name; ?></div>

                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>Dovuto :</strong>
                                <?php echo '  '. money_format('%.2n', $order->netTotal) . ' &euro;'; ?></div>

                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>Pagato :</strong>
                                <?php echo '  '. (!is_null($order->paidAmount)) ? money_format('%.2n', $order->paidAmount) .' &euro;': '0.00 &euro;'; ?></div>

                        </div>
                        <div><br>
                            <div class="pull-left font-montserrat small"><strong>Stato dell'ordine :</strong>
                                <?php echo '  '. $orderStatus; ?></div>

                       <br>
                        <div class="pull-left font-montserrat small"><strong>Sconto Coupon :</strong>
                            <?php echo ' ' . money_format('%.2n', $order->couponDiscount) . ' &euro;'; ?>

                        </div><br>
                        <div class="pull-left font-montserrat small"><strong>Sconto Utente :</strong>
                            <?php echo ' ' . money_format('%.2n', $order->userDiscount) . ' &euro;'; ?>

                        </div><br>
                        <div class="pull-left font-montserrat small"><strong>Modifica Pagamento :</strong>
                            <?php echo ' ' . money_format('%.2n', $order->paymentModifier) . ' &euro;'; ?>

                        </div><br>
                        <div class="pull-left font-montserrat small"><strong>Spese di spedizione :</strong>
                            <?php echo ' ' . money_format('%.2n', $order->shippingPrice) . ' &euro;'; ?>
                        </div>
                    </div>
                    </div>

                    <div class="pull-right sm-m-t-20">

                    <div class="col-md-9 col-sm-height sm-no-padding">
                        <p class="small no-margin">Indirizzo di fatturazione</p>
                        <h2 class="semi-bold m-t-0"><?php echo $userAddress->surname . ' ' . $userAddress->name; ?></h2>
                        <address>
                            <strong><?php echo (!empty($userAddress->company)) ? ' <br>' . $userAddress->company : null; ?>
                           <?php echo $userAddress->address; ?>
                            <br><?php echo $userAddress->postcode . ' ' . $userAddress->city . ' (' . $userAddress->province . ')'; ?>
                            <br><?php echo $userAddress->country->name; ?>
                            <br><?php (!is_null ($order->user->userDetails->fiscalCode)) ? 'C.FISC. o P.IVA: ' . $order->user->userDetails->fiscalCode : null; ?></strong>
                        </address>
                    <div class="clearfix"></div><br>
                        <p class="small no-margin">Indirizzo di spedizione</p>
                        <address>
                            <strong><?php echo $userShipping->surname . ' ' . $userShipping->name; ?>
                               <?php echo (!empty($userShipping->company)) ? '<br>' . $userShipping->company : null; ?>
                            <br><?php echo $userShipping->address; ?>
                            <br><?php echo $userShipping->postcode . ' ' . $userShipping->city . ' (' . $userShipping->province . ')'; ?>
                            <br><?php echo $userShipping->country->name; ?>
                            <br><?php echo 'Email: ' . $order->user->email; ?>
                            <br><?php echo (!is_null($order->user->userDetails->phone)) ? 'Tel: ' . $order->user->userDetails->phone : 'Tel: ---'; ?>
                            </strong>
                        </address>
                    </div>

                    </div>

                <div class="clearfix"></div>
                    <table class="table m-t-30">
                        <thead>
                        <!--tabella prodotti-->
                        <tr>
                            <th class="font-montserrat small">Riga</th>
                            <th class="font-montserrat small">Sku</th>
                            <th class="text-center font-montserrat small">Stato Riga</th>
                            <th class="font-montserrat small">Descrizione Prodotto</th>
                            <th class="text-center font-montserrat small">Foto</th>
                            <th class="text-center font-montserrat small">Brand</th>
                            <th class="text-center font-montserrat small">Shop</th>
                            <th class="text-center font-montserrat small">Taglia</th>
                            <th class="text-center font-montserrat small">Variante</th>
                            <th class="text-center font-montserrat small">Importo</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php $tot =0;
                        $i=0;
                        foreach ($order->orderLine as $orderLine) {
                            if ($i>0) {
                                echo '<div class="page-break"></div>';
                            }
                            $i++;
                            ?>
                        <tr><td class="text-center"><?php echo $orderLine->id; ?></td>
                                 <?php $productSku = \bamboo\domain\entities\CProductSku::defrost($orderLine->frozenProduct);

                                 $productNameTranslation = $productSku->product->getName(); ?>
                            <td class=""><?php echo $productSku->printPublicSku(); ?></td>
                            <td class="text-center">
                                <?php foreach ($statusesLine as $statusLine){
                                    if ($statusLine->code == $orderLine->status) {
                                        echo $statusLine->title;
                                    }
                                } ?></td>
                            <td><?php echo '<p class="text-black">' . $productNameTranslation; ?></p></td>
                            <td><img width="90" src="<?php echo $app->image($productSku->product->getPhoto(1,281),'amazon') ?>" /></td>
                            <td class="text-center"><?php echo $productSku->product->productBrand->name; ?></td>
                            <td class="text-center"><?php echo $productSku->shop->name; ?></td>
                            <td class="text-center"><?php echo $productSku->productSize->name; ?></td>
                            <td class="text-center"><?php echo $productSku->product->productVariant->name; ?></td>
                            <td class="text-center"><?php
                                $tot += $orderLine->activePrice;
                                echo money_format('%.2n', $orderLine->activePrice) . ' &euro;'; ?></td>
                        </tr>
                        <?php
                        } ?>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</div>
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