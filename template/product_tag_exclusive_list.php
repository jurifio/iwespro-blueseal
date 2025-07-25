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
                               data-datatable-name="product_tag_exclusive_list"
                               data-controller="ProductTagExclusiveListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                                <tr>
                                    <th data-slug="code"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Codice</th>
                                    <th data-slug="shop"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Shop</th>
                                    <th data-slug="colorGroup"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Gruppo Colore</th>
                                    <th data-slug="season"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Stagione</th>
                                    <th data-slug="details"
                                        data-searchable="false"
                                        data-orderable="false"
                                        class="center">Dettagli</th>
                                    <th data-slug="brand"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Brand</th>
                                    <th data-slug="tag"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Sezioni Esclusive</th>
                                    <th data-slug="isOnSale"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Sale</th>
                                    <th data-slug="available"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Disp</th>
                                    <th data-slug="status"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">Stato</th>
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
    <bs-toolbar-group data-group-label="Gestione Sezioni">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-tag"
            data-permission="/admin/product/edit"
            data-event="bs.product.exclusive.tag"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Inserisci i prodotti nelle Sezioni"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-sort-numeric-asc"
                data-permission="/admin/product/edit"
                data-event="bs.priority.edit"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Priorità"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-chain"
                data-permission="/admin/product/edit"
                data-event="bs.exclusivetag.all"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Inserisci i Prodotti nella sezione tutti i Prodotti"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>