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
                                <th class="center">Code</th>
                                <th class="center">Shop</th>
                                <th class="center">ExtID</th>
                                <th class="center">Immagine</th>
                                <th class="center">Brand</th>
                                <th class="center">Stato</th>
                                <th class="center">Creazione</th>
                                <th class="center">Problemi</th>
                                <th class="center">Strumenti</th>
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
</html>