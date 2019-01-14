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
                        <h2>BETA VERSION | 1.0</h2>
                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="size_full_list"
                               data-controller="ProductWorkInvoiceListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="completeName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Foison</th>
                                <th data-slug="number"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero</th>
                                <th data-slug="totalWithVat"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Totale fattura da Operator</th>
                                <th data-slug="effectiveTotal"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Totale fattura</th>
                                <th data-slug="invoiceTypeName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Tipo di fattura</th>
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
    <bs-toolbar-group data-group-label="Download fattura">
        <bs-toolbar-button
                data-remote="bs.friend.order.invoice.download"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>