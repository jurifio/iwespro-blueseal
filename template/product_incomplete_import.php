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
                        <div class="pull-right"><?php if ($app->user()->hasPermission('/admin/product/add')): ?><a
                                href="<?php echo $modifica; ?>">
                                    <button class="btn btn-complete btn-cons">Aggiungi <i class="fa fa-plus"></i>
                                    </button></a><?php endif; ?></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped" data-datatable-name="product_importer_list"
                               data-controller="ProductImporterProblemsListController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>" id="productWithImporterProblems">
                            <thead>
                            <tr>
                                <th class="center">Codice</th>
                                <th class="center">Shop</th>
                                <th class="center">CPF</th>
                                <th class="center">Immagine</th>
                                <th class="center">Brand</th>
                                <th class="center">Stato</th>
                                <th class="center">Creazione</th>
                                <th class="center">Problemi</th>
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
                data-download="job-2-<?php echo date('YmdHmi') ?>.log"
                data-href="/blueseal/xhr/JobLogDownloadController?job=2"
                data-title="Scarica Log"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</html>