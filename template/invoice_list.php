<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
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
                               data-datatable-name="invoice_list"
                               data-controller="InvoiceListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">id
                                </th>
                                <th data-slug="invoiceType"
                                    data-searchable="true"
                                    data-orderable="true" class="center">invoiceType
                                </th>
                                <th data-slug="invoiceSiteChar"
                                    data-searchable="true"
                                    data-orderable="true" class="center">invoice Sito
                                </th>
                                <th data-slug="invoiceNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero Fattura
                                </th>
                                <th data-slug="invoiceDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Data Fattura
                                </th>
                                <th data-slug="orderId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero Ordine
                                </th>
                                <th data-slug="customerName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cliente
                                </th>

                                <th data-slug="invoiceShopId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop
                                </th>
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
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Fatture">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-eraser"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Cancella Fattura (!!!)"
                data-placement="bottom"
                data-event="bs.invoice.delete"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>