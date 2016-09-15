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
                               data-datatable-name="marketplace_product_list"
                               data-column-filter="true"
                               data-controller="MarketplaceProductListAjaxController<?php echo $queryString ?>"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                                <tr>
                                    <th class="center">Codice</th>
                                    <th class="center">Shop</th>
                                    <th class="center">Brand</th>
                                    <th class="center">Stock</th>
                                    <th class="center">Season</th>
                                    <th class="center">CPF</th>
                                    <th class="center">Immagine</th>
                                    <th class="center">Fee</th>
                                    <th class="center">Marketplaces</th>
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
            data-tag="a"
            data-icon="fa-share"
            data-permission="/admin/product/edit"
            data-event="bs.product.publish"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Pubblica prodotto sui marketplace"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-code-o"
            data-permission="/admin/product/edit"
            data-event="bs.product.response"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Leggi Stato"
            data-placement="bottom"
            data-toggle="modal"
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
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>