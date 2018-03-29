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
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="shooting_details"
                               data-controller="ShootingDetailsListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500"
                                data-shootingid="<?php echo $shooting ?>">
                            <thead>
                            <tr>
                                <th data-slug="progressiveLineNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero progressivo</th>
                                <th data-slug="shootingId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shooting</th>
                                <th data-slug="DT_RowId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prodotto</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data di creazione</th>
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop</th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione</th>
                                <th data-slug="externalId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Origin. ID</th>
                                <th data-slug="cpf"
                                    data-searchable="true"
                                    data-orderable="true" class="center">cpf</th>
                                <th data-slug="dummy"
                                    data-searchable="true"
                                    data-orderable="true" class="center">dummy</th>
                                <th data-slug="productBrandName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand</th>
                                <th data-slug="hasQty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Quantità disponibile</th>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
                data-remote="bs.product.print.aztec"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>