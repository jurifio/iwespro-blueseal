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
                               data-special-name="<?php echo $marketplaceName ?>"
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
                                        data-searchable="true"
                                        data-orderable="true" class="center categoryFilterType">Categorie</th>
                                    <th data-slug="stock"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Stock</th>
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
                                    <th data-slug="isDeleted"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Cancellato</th>
                                    <th data-slug="isToWork"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Da Lavorare</th>
                                    <th data-slug="hasError"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Errore</th>
                                    <th data-slug="creationDate"
                                        data-searchable="true"
                                        data-orderable="true" class="center dateTypeFilter">Creazione</th>
                                    <th data-slug="visits"
                                        data-searchable="true"
                                        data-orderable="true" class="center">N° Visite</th>
                                    <th data-slug="conversions"
                                        data-searchable="true"
                                        data-orderable="true" class="center">N° Conversioni</th>
                                    <th data-slug="pConversions"
                                        data-searchable="true"
                                        data-orderable="true" class="center">N° Conversioni Proprie</th>
                                    <th data-slug="visitsCost"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Costo Visite (Stimato)</th>
                                    <th data-slug="conversionsValue"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Valore Conversioni</th>
                                    <th data-slug="pConversionsValue"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Valore Conversioni Proprie</th>
                                    <th data-slug="activePrice"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Prezzo Attivo</th>
                                    <th data-slug="ordersIds"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Ordini</th>
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
            data-remote="bs.productHasMarketplace.marketplace.delete"
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
            data-remote="btn.datatable.date.filter"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Esportazione">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>