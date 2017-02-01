<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <style type="text/css">
        body {zoom: 100%;}
        @page {
            size:A4;
            margin:5mm 0mm 0mm 0mm;
        }
        @media print {
            body {zoom: 100%;}

            .newpage {
                page-break-before: always;
                page-break-after: always;
                page-break-inside: avoid;
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
        }
    </style>
</head>
<body class="fixed-header">
<div class="container-fluid container-fixed-lg">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="invoice padding-50 sm-padding-10">
                <div>
                    <div class="pull-left">
                        <!--logo negozio-->
                        <?php if ($logo) : ?>
                        <img height="60" alt="" class="invoice-logo" src="<?php echo $logo ?>" />
                        <?php endif; ?>
                        <!--indirizzo negozio-->
                        <br><br />
                        <address class="m-t-10"><strong><?php echo $addressBookEmitter->subject ?></strong>
                            <br /><?php echo $addressBookEmitter->address; ?>
                            <br /><?php echo $addressBookEmitter->city; ?> ( <?php $addressBookEmitter->province ?> )
                            <br /><?php echo $addressBookEmitter->postcode; ?>
                            <br /><?php echo $addressBookEmitter->phone; ?>
                        </address>
                        <br />
                        <div>
                            <div class="pull-left font-montserrat all-caps small"><strong>Fattura N.:</strong>
                                <?php echo $invoice->number; ?> <strong> del </strong> <?php echo \bamboo\utils\time\STimeToolbox::EurFormattedDate($invoice->date);?></div>
                        </div><br />
 <!--                       <div>
                            <div class="pull-left font-montserrat small" ><strong>Rif. ordine N.:</strong>
                                </div>
                        </div>
                        <div><br />
                            <div class="pull-left font-montserrat small"><strong>Metodo di pagamento:</strong>
                               </div>
                        </div> -->
                    </div>
                    <div class="pull-right sm-m-t-0">
                        <h2 class="font-montserrat all-caps hint-text">Fattura</h2>

                    <div class="col-md-9 col-sm-height sm-padding-20">
                        <p class="small no-margin">Intestata a</p>
                        <h5 class="semi-bold m-t-0 no-margin"><?php echo $addressBookRecipient->subject; ?></h5>
                        <address>
                           <?php echo $addressBookRecipient->address; ?>
                            <br /><?php echo $addressBookRecipient->postcode . ' ' . $addressBookRecipient->city . ' (' . $addressBookRecipient->province . ')'; ?>
                            <br /><?php echo $addressBookRecipient->country->name; ?>
                            <br /><?php echo (!is_null($addressBookRecipient->vatNumber)) ? 'C.FISC. o P.IVA: ' . $addressBookRecipient->vatNumber : ''; ?></strong>
                        </address>
                    <!--<div class="clearfix"></div><br>
                        <p class="small no-margin">Indirizzo di spedizione</p>
                        <address>
                            <strong><?php // echo $userShipping->surname . ' ' . $userShipping->name; ?>
                               <?php //echo (!empty($userShipping->company)) ? '<br>' . $userShipping->company : null; ?>
                            <br /><?php // echo $userShipping->address; ?>
                            <br /><?php // echo $userShipping->postcode . ' ' . $userShipping->city . ' (' . $userShipping->province . ')'; ?>
                            <br /><?php // echo $userShipping->country->name; ?></strong>
                        </address>
                    </div>-->

                    </div>
                </div>
                    <table class="table invoice-table m-t-0">
                        <thead>
                        <!--tabella prodotti-->
                        <tr>
                            <th class="small">Descrizione Prodotto</th>
                            <th class="text-center small">Importo</th>
                            <th class="small text-center">IVA %</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($invoice->invoiceLine as $line) {
                            ?>
                        <tr>
                            <td class="">
                                <span class="small"><?php echo $line->description;  ?></span>
                            </td>
                            <td class="text-center">
                                <?php echo \bamboo\utils\price\SPriceToolbox::formatToEur($line->priceNoVat); ?> &euro;
                            </td>
                            <td>
                                <?php echo $line->vat; ?>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <table class="table invoice-table m-t-0">
                        <tbody>
                        <tr class="text-left font-montserrat small">
                            <td style="width: 60%" class="text-right"><strong>Totale (senza IVA)</strong></td>
                            <td style="border: 0px" class="text-right"><?php echo $noVatTotal; ?> &euro;</td>
                        </tr>
                        <tr style="border: 0px" class="text-left font-montserrat small hint-text">
                            <?php foreach($imponibili as $k => $v) : ?>
                            <td style="width: 60%" class="text-right">Imponibile<br /></td>
                            <td class="text-right" width="25%">IVA <?php echo $k; ?>%<br /><?php echo \bamboo\utils\price\SPriceToolbox::formatToEur($v); ?> &euro;</td>
                            <?php endforeach; ?>
                        </tr>
                        <td style="width: 60%" class="text-right"><strong>Totale (con IVA)</strong></td>
                        <td style="border: 0px" class="text-right"><?php echo $invoice->totalWithVat . ' &euro;'; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            <div style="text-align: center">
                <img alt="" class="invoice-thank" data-src-retina='/assets/img/invoicethankyou.jpg' data-src='/assets/img/invoicethankyou.jpg' src='/assets/img/invoicethankyou.jpg'>
            </div>
            <br>
            <br>
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