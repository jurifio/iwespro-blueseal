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
                               data-datatable-name="sale_price_product_sku_utility_list"
                               data-controller="SalePriceProductSkuListAjaxControllerUtility"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">ID</th>
                                <th data-slug="size"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Taglia</th>
                                <th data-slug="on_sale"
                                    data-searchable="true"
                                    data-orderable="true" class="center">In saldo</th>
                                <th data-slug="p_price"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo</th>
                                <th data-slug="p_sale_price"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prezzo in saldo</th>
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
    <bs-toolbar-group data-group-label="Strumenti Colonna">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-word-o"
                data-permission="/admin/content/add"
                data-event="bs-sale-price-modify"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Modifica saldo"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-external-link"
                data-permission="/admin/content/add"
                data-event="bs-sale-price-go-modify-price"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Modifica prezzo"
                data-placement="bottom"
        ></bs-toolbar-button>
        <!--<bs-toolbar-button
                data-tag="a"
                data-icon="fa-arrows-h"
                data-permission="/admin/content/add"
                data-event="bs-emergency-alignment"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Allinea prezzi totali e con saldo nei prodotti pubblici"
                data-placement="bottom"
        ></bs-toolbar-button>-->
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>