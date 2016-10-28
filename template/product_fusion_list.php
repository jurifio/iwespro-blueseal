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
                        <table class="table table-striped responsive" width="100%" data-column-filter="true" data-datatable-name="product_fusion_list" data-controller="ProductFusionListAjaxController" data-url="<?php echo $app->urlForBluesealXhr() ?>" >
                            <thead>
                            <tr>
                                <th class="center">Codice</th>
                                <th class="center">Brand</th>
                                <th class="center">CPF</th>
                                <th class="center">Variante</th>
                                <th class="center">Shops</th>
                                <th class="center">Gruppo taglie</th>
                                <th class="center">Stato</th>
                                <th class="center">Immagine</th>
                                <th class="center">Skus</th>
                                <th class="center">Listino</th>
                                <th class="center">Valore Vendite</th>
                                <th class="center">Qty Venduta</th>
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
    <bs-toolbar-group data-group-label='Fusione Prodotti'>
        <bs-toolbar-button
            data-remote="bs.product.merge"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>