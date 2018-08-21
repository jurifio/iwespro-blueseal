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
                                <th data-slug="descr"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Descrizione</th>
                                <th data-slug="workCategory"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Lavorazione</th>
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
                                <th data-slug="unfitDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data di notifica non idoneità</th>
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
                                <th data-slug="numberOfProduct"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero di prodotti</th>
                                <th data-slug="finish"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Norm ok</th>
                                <th data-slug="todo"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Norm da fare</th>
                                <th data-slug="documentId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fattura</th>
                                <th data-slug="workCategoryId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Categoria</th>
                                <th data-slug="workPriceListName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo</th>
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
    <?php if($allShops):?>
    <bs-toolbar-group data-group-label="Nuovo lotto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.empty.product.batch"
                data-title="Crea un nuovo lotto"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Conferma termine lotto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-step-forward"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.end.product.batch"
                data-title="Conferma termine lotto"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-stop"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.unfit.batch"
                data-title="Notifica lotto non idoneo"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
        <bs-toolbar-group data-group-label="Associa lotto a fason">
            <bs-toolbar-button
                data-tag="a"
                data-icon="fa-arrows-h"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.product.batch.to.fason"
                data-title="Associa lotto a fason"
                data-placement="bottom"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    <?php endif; ?>
    <bs-toolbar-group data-group-label="Carica fattura per i lotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-cloud-upload"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.end.invoice.product"
                data-title="Carica la fattura"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Accetta il lotto assegnato">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-check-square"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.accept.product.batch"
                data-title="Accetta il lotto"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>