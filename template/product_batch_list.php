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
                               data-datatable-name="size_full_list"
                               data-controller="ProductBatchListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data di creazione</th>
                                <th data-slug="scheduledDelivery"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data prevista consegna</th>
                                <th data-slug="confirmationDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data di conferma lotto</th>
                                <th data-slug="closingDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data di chiusura</th>
                                <th data-slug="value"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Valore lotto</th>
                                <th data-slug="paid"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Pagato</th>
                                <th data-slug="sectional"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Sezionale</th>
                                <th data-slug="foison"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Foison</th>
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
</bs-toolbar>
</body>
</html>