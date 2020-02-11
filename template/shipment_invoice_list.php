<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php";?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>
            <input type="hidden" id="lastInvoiceDate" name="lastInvoiceDate" value="<?php echo $dateInvoice ?>"/>
            <input type="hidden" id="shipmentInvoiceNumberTemp" name="shipmentInvoiceNumberTemp" value="<?php echo $shipmentInvoiceNumber ?>"/>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="shipment_list"
                               data-controller="ShipmentInvoiceListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="dateInvoice"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Fattura Spedizioniere</th>
                                <th data-slug="shipmentInvoiceNumber"
                                    data-is-visible="true"
                                    data-searchable="true"
                                    data-orderable="false" class="center">Numero di fattura</th>
                                <th data-slug="carrier"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Carrier</th>
                                <th data-slug="totalShipment"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Totale Spedizioni</th>
                                <th data-slug="impFat"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Imponibile Fattura</th>
                                <th data-slug="iva"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Iva</th>
                                <th data-slug="totFat"
                                    data-searchable="true"
                                    data-orderable="true" class="center">totale Fattura</th>
                            </tr>

                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>

</body>
</html>