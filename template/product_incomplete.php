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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">
                    </div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped" data-datatable-name="product_incomplete_list"
                               data-controller="ProductIncompleteListController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               id="productWithProblems"
                               data-inner-setup="true">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Id</th>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Code</th>
                                <th data-slug="shop"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shop</th>
                                <th data-slug="season"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Stagione</th>
                                <th data-slug="dummyPicture"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Immagine</th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Brand</th>
                                <th data-slug="status"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stato</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Creazione</th>
                                <th data-slug="problems"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Cosa manca</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'parts/footer.php'; ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni sui prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-magic"
            data-permission="/admin/product/edit"
            data-event="bs.manage.sizeGroups"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Assegna Gruppi taglie"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>