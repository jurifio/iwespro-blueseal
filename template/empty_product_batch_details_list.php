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
                               data-datatable-name="empty_product_batch_list"
                               data-controller="EmptyProductBatchDetailsListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500"
                               data-productbatchid="<?php echo $productBatchId ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="productCode"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prodotto</th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand</th>
                                <th data-slug="productName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome del prodotto</th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione</th>
                                <th data-slug="description"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Descrizione</th>
                                <th data-slug="colorGroup"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Gruppo colore</th>
                                <th data-slug="colorNameManufacturer"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Colore produttore</th>
                                <th data-slug="details"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Dettagli</th>
                                <th data-slug="dummy"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Immagine</th>
                                <th data-slug="categoryId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Categorie</th>
                                <th data-slug="stepName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fase</th>
                                <th data-slug="productCard"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Foto per normalizzazione</th>
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

    <bs-toolbar-group data-group-label="Elimina prodotti">
        <bs-toolbar-button
                data-remote="bs.delete.product.from.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>