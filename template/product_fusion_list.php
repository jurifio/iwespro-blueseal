<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%" data-column-filter="true"
                               data-datatable-name="product_fusion_list"
                               data-controller="ProductFusionListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-lenght-menu="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Codice</th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Brand</th>
                                <th data-slug="CPF"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">CPF</th>
                                <th data-slug="variant"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Variante</th>
                                <th data-slug="shops"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shops</th>
                                <th data-slug="sizeGroup"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Gruppo taglie</th>
                                <th data-slug="status"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stato</th>
                                <th data-slug="dummyPicture"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Immagine</th>
                                <th data-slug="skus"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Skus</th>
                                <th data-slug="price"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Listino</th>
                                <th data-slug="income"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Valore Vendite</th>
                                <th data-slug="sells"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Qty Venduta</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php" ?>
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