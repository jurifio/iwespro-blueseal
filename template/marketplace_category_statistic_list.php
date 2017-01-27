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
                               data-datatable-name="marketplace_product_static_list"
                               data-controller="MarketplaceCategoryStatisticListAjaxController<?php echo $queryString ?>"
                               data-special-name="<?php echo $marketplaceName ?>"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="25,100,200,500">
                            <thead>
                                <tr>
                                    <th data-slug="category"
                                        data-searchable="true"
                                        data-orderable="true" class="center categoryFilterType">Categoria</th>
                                    <th data-slug="visits"
                                        data-searchable="true"
                                        data-orderable="true" class="center">N° Visite</th>
                                    <th data-slug="products"
                                        data-searchable="true"
                                        data-orderable="true" class="center">N° Prodotti</th>
                                    <th data-slug="conversions"
                                        data-searchable="true"
                                        data-orderable="true" class="center">N° Conversioni</th>
                                    <th data-slug="pConversions"
                                        data-searchable="true"
                                        data-orderable="true" class="center">N° Conversioni Proprie</th>
                                    <th data-slug="visitsCost"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Costo Visite (Stimato)</th>
                                    <th data-slug="conversionsValue"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Valore Conversioni</th>
                                    <th data-slug="pConversionsValue"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Valore Conversioni Proprie</th>
                                    <th data-slug="cos"
                                        data-searchable="true"
                                        data-orderable="true" class="center">COS</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Filtra">
        <bs-toolbar-button
            data-remote="btn.datatable.date.filter"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Esportazione">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>