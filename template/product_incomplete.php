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
                            <table class="table table-striped" data-datatable-name="product_incomplete_list" data-controller="ProductIncompleteListController" data-url="<?php echo $app->urlForBluesealXhr() ?>" id="productWithProblems">
                                <thead>
                                <tr>
                                    <th class="center">Id</th>
                                    <th class="center">Code</th>
                                    <th class="center">Shop</th>
                                    <th class="center">Stagione</th>
                                    <th class="center">Immagine </th>
                                    <th class="center">Brand</th>
                                    <th class="center">Stato</th>
                                    <th class="center">Creazione</th>
                                    <th class="center">Cosa manca</th>
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