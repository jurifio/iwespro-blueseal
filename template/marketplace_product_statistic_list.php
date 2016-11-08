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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="marketplace_product_static_list"
                               data-controller="MarketplaceProductStatisticListAjaxController<?php echo $queryString ?>"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="25,100,200,500">
                            <thead>
                                <tr>
                                    <th data-slug="codice"
                                        data-searchable="true"
                                        data-orderable="true" class="center"
                                        data-default-order="desc">Codice</th>
                                    <th data-slug="shop"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Shop</th>
                                    <th data-slug="brand"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Brand</th>
                                    <th data-slug="categories"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Categorie</th>
                                    <th data-slug="stock"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Stock</th>
                                    <th data-slug="season"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Season</th>
                                    <th data-slug="itemno"
                                        data-searchable="true"
                                        data-orderable="true" class="center">CPF</th>
                                    <th data-slug="dummy"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Immagine</th>
                                    <th data-slug="fee"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Fee</th>
                                    <th data-slug="creationDate"
                                        data-searchable="true"
                                        data-orderable="true" class="center dateTypeFilter">Creazione</th>
                                    <th data-slug="visitDate"
                                        data-searchable="true"
                                        data-orderable="true" class="center dateTypeFilter">Data Visita</th>
                                    <th data-slug="visits"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Visite</th>
                                    <th data-slug="conversions"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Conversioni</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Pubblica Prodotto">
        <bs-toolbar-button
            data-remote="bs.product.marketplace.publish"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.marketplace.response"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-repeat"
            data-permission="/admin/product/edit"
            data-event="bs.product.retry"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Riprova Inserimento"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.marketplace.publish.all"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Assegnazione Ean">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-barcode fa-plus"
            data-permission="/admin/product/edit"
            data-event="bs.ean.newRange"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Inserisci nuovo Range Ean"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-barcode fa-share"
            data-permission="/admin/product/edit"
            data-event="bs.product.ean"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Assegna nuovi Ean Prodotti"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Filtra">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-filter"
            data-permission="/admin/product/edit"
            data-event="bs.marketplace.filter"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Filtra Tabella"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-filter"
            data-permission="/admin/product/edit"
            data-class="btn btn-default datePicker"
            data-load-event="bs.dateinput.load"
            data-rel="tooltip"
            data-title="Seleziona Date"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>