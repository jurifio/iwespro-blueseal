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

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="shipment_list"
                               data-controller="ShipmentListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice</th>
                                <?php if(count($shops) > 1): ?>
                                    <th data-slug="shop"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Shop</th>
                                <?php endif; ?>
                                <th data-slug="carrier"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Carrier</th>
                                <th data-slug="bookingNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Booking</th>
                                <th data-slug="trackingNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tracking</th>
                                <th data-slug="fromAddress"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Da</th>
                                <th data-slug="toAddress"
                                    data-searchable="true"
                                    data-orderable="true" class="center">A</th>
                                <th data-slug="predictedShipmentDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Data Spedizione Prevista</th>
                                <th data-slug="shipmentDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Data Spedizione</th>
                                <th data-slug="predictedDeliveryDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Data Consegna Prevista</th>
                                <th data-slug="deliveryDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Data Consegna</th>
                                <th data-slug="cancellationDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Cancellazione</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Data Creazione</th>
                                <th data-slug="note"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Note</th>
                                <th data-slug="productContent"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Contenuto Prodotti</th>
                                <th data-slug="orderContent"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Contenuto Ordini</th>
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
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione spedizioni">
        <bs-toolbar-button
                data-remote="btn.add.shipmentToUs"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.shipment.tracking.update"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.shipment.shipped.time"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.shipment.delivery.time"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.shipment.cancel"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>