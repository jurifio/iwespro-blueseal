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
                               data-datatable-name="catalog_list"
                               data-column-filter="true"
                               data-controller="StorehouseOperationAjaxListController:"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-lenght-menu="100, 200, 500">
                            <thead>
                                <tr>
                                    <th data-slug="id"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Id</th>
                                    <th data-slug="creationDate"
                                        data-searchable="false"
                                        data-orderable="false"
                                        data-default-order="desc" class="center">Data Creazione</th>
                                    <th data-slug="operationDate"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Data Operazione</th>
                                    <th data-slug="friend"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Friend</th>
                                    <th data-slug="cause"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Causale</th>
                                    <th data-slug="movements"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Movimenti</th>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-exchange"
            data-permission="/admin/product/list"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo movimento"
            data-placement="bottom"
            data-href="/blueseal/prodotti/movimenti/inserisci"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-barcode"
            data-permission="/admin/product/list"
            data-event="bs.print.barcode"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Stampa barcode"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>