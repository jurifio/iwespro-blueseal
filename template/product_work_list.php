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
                        <h2>BETA VERSION | 1.0</h2>
                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="product_work_list"
                               data-controller="ProductWorkListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="productVariantId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Variante</th>
                                <th data-slug="productStatus"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato</th>
                                <th data-slug="dummyPicture"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Img</th>
                                <th data-slug="productBrand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand</th>
                                <th data-slug="productSeason"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione</th>
                                <th data-slug="productCard"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Foto scheda</th>
                                <th data-slug="productBatchNumber"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Lotto</th>
                                <th data-slug="categoryId"
                                    data-searchable="true"
                                    data-orderable="true" class="center categoryFilterType">Categorie</th>
                                <th data-slug="productName"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Nome</th>
                                <th data-slug="colorGroup"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Gruppo Colore</th>
                                <th data-slug="colorNameManufacturer"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Colore Produttore</th>
                                <th data-slug="shop"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop</th>
                                <th data-slug="hasDetails"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ha Dett.</th>
                                <th data-slug="pDescTranslate"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Descrizione Dettagli</th>
                                <th data-slug="details"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Dettagli
                                </th>
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
    <bs-toolbar-group data-group-label="Lotti">
        <bs-toolbar-button
                data-remote="bs.delete.product.from.batch.work.list"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.addBatch"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.addBatch.empty"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Assegna prodotti con lettore">
    <bs-toolbar-button
            data-remote="bs.product.addBatch.massive"
    ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Modifiche">
    <bs-toolbar-button
            data-remote="bs.product.category.change"
    ></bs-toolbar-button>
    <bs-toolbar-button
            data-remote="bs.product.namesMerge"
    ></bs-toolbar-button>
    <bs-toolbar-button
            data-remote="bs.product.editVariantDescription"
    ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>