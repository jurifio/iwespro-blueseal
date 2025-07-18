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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="prestashop_marketplace_product"
                               data-controller="MarketplacePrestashopProductListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="25,100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="productCode"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Codice prodotto
                                </th>
                                <th data-slug="dummy"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Foto
                                </th>
                                <th data-slug="shop"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop
                                </th>
                                <th data-slug="brand"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">brand
                                </th>
                                <th data-slug="externalId"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Cpf
                                </th>

                                <th data-slug="price"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Prezzi Shop<br>pr.Pieno(pr.Saldo)-stato Saldo
                                </th>
                                <th data-slug="marketplaceAssociation"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Associazioni<br>Marketplace
                                </th>
                                <th data-slug="status"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Stato Pubblicazione<br>Marketplace
                                </th>

                                <th data-slug="productStatusMarketplaceId"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Origine di<br>Pubblicazione
                                </th>

                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione
                                </th>
                                <th data-slug="totalQty"
                                    data-searchable="true"
                                    data-orderable="true" class="center">quantità Totali
                                </th>
                                <th data-slug="stock"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stock
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Prodotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-exchange"
                data-permission="/admin/product/edit"
                data-event="bs.adding.presta"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica Prodotti su marketPlace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.marketplace.unpublish"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-space-shuttle"
                data-permission="/admin/product/edit"
                data-event="bs.add.presta.product.all"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica Tutti Prodotti su marketPlace con stato pubblicato"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Emulatori Jobs">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-flag"
                data-permission="/admin/product/edit"
                data-event="bs.marketplace.prepare.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Prepara prodotti per marketplace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-flag-checkered"
                data-permission="/admin/product/edit"
                data-event="bs.marketplaceaccountrule.publish.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica prodotti per Account asssociato a Marketplace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>