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
                                <th data-slug="price"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Prezzo
                                </th>
                                <th data-slug="marketplaceAssociation"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Associazioni
                                </th>
                                <th data-slug="status"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Stato
                                </th>
                                <th data-slug="sale"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Saldo
                                </th>
                                <th data-slug="pickySale"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Saldo in Picky
                                </th>
                                <th data-slug="prestaId"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Prestashop Id
                                </th>
                                <th data-slug="cronjobReservation"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Marketplace prossimo inserimento
                                </th>
                                <th data-slug="cronjobOperation"
                                    data-required="true"
                                    data-searchable="true"
                                    data-orderable="true" class="center"
                                    data-default-order="desc">Operazioni previste dal cron
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
    <bs-toolbar-group data-group-label="Pubblica Prodotto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-exchange"
                data-permission="/admin/product/edit"
                data-event="bs.add.presta.product"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Pubblica Prodotti su marketPlace"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-dollar"
                data-permission="/admin/product/edit"
                data-event="bs.marketplace.product.sale"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Metti in saldo i prodotti"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>

</bs-toolbar>
</body>
</html>