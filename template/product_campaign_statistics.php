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
                               data-datatable-name="product_campaign_statistics"

                               data-inner-setup="true"
                               data-controller="ProductCampaignStatisticsAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Codice</th>
                                <th data-slug="shops"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shops</th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione</th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand</th>
                                <th data-slug="categories"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Categorie</th>
                                <th data-slug="first"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prima Visita</th>
                                <th data-slug="last"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center">Ultima Visita</th>
                                <th data-slug="pageView"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Visite</th>
                                <th data-slug="conversions"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Conversioni</th>
                                <th data-slug="campaign"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Campagna</th>
                                <th data-slug="firstest"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Primissima Visita</th>
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
            data-remote="btn.href.add.product"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>