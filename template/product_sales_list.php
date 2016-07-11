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
                        <table class="table table-striped responsive" width="100%" data-column-filter="true" data-datatable-name="product_sales_list" data-controller="ProductSalesListAjaxController" data-url="<?php echo $app->urlForBluesealXhr() ?>" >
                            <thead>
                            <tr>
                                <th class="center">Codice1</th>
                                <th class="center">Brand</th>
                                <th class="center">CPF</th>
                                <th class="center">Categorie</th>
                                <th class="center">Stagione</th>
                                <th class="center">Variante</th>
                                <th class="center">Immagine</th>
                                <th class="center">Skus</th>
                                <th class="center">Shops</th>
                                <th class="center">Listino</th>
                                <th class="center">Scon.</th>
                                <th class="center">% Sconto</th>
                                <th class="center">ScA</th>
                                <th class="center">Costo Friend</th>
                                <th class="center">Costo Friend Sale</th>
                                <th class="center">Costo F. Pre. Stag.</th>
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
    <bs-toolbar-group data-group-label='Gestione delle Promozioni'>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-shopping-cart"
            data-permission="/admin/product/edit"
            data-event="bs.sales.set"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Imposta le promozioni"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-dollar"
            data-permission="/admin/product/edit"
            data-event="bs.sales.price"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Definisci gli sconti"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>