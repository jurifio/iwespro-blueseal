<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
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
                    <div class="panel-heading">
                        <div class="panel-title">Elenco prodotti</div>
                        <div class="export-options-container pull-right"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped" data-column-filter="true" data-datatable-name="product_importer_list"
                               data-controller="ProductImporterProblemsListController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500"
                               id="productWithImporterProblems">
                            <thead>
                            <tr>
                                <th data-slug="productCode"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Codice</th>
                                <th data-slug="shop"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shop</th>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">CPF</th>
                                <th data-slug="sizeGroup"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Gr. Taglie</th>
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
                                    data-searchable="false"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Creazione</th>
                                <th data-slug="problems"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Problemi</th>
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
</body>
<bs-toolbar>
    <bs-toolbar-group data-group-label="Log ultimo inserimento">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-long-arrow-down"
                data-permission="/admin/product/add"
                data-event="bs.log.download"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-download="job-2-<?php echo date('YmdHmi') ?>.csv"
                data-href="/blueseal/xhr/JobLogDownloadController?job=2"
                data-title="Scarica Log"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni sui prodotti">
        <bs-toolbar-button
            data-remote="bs.product.sizeGroup.change"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</html>