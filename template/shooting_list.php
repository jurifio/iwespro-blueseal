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
                               data-datatable-name="size_full_list"
                               data-controller="ShootingListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="date"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data</th>
                                <th data-slug="friendDdt"
                                    data-searchable="true"
                                    data-orderable="true" class="center">DDT Friend</th>
                                <th data-slug="pickyDdt"
                                    data-searchable="true"
                                    data-orderable="true" class="center">DDT Picky</th>
                                <th data-slug="note"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Note</th>
                                <th data-slug="phase"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fase</th>
                                <th data-slug="pieces"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Numero di colli</th>
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop</th>
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
    <!--
    <bs-toolbar-group data-group-label="Stampa DDT (Friend)">
        <bs-toolbar-button
                data-remote="bs.product.create.ddt"
        ></bs-toolbar-button>
    </bs-toolbar-group> -->
</bs-toolbar>
</body>
</html>