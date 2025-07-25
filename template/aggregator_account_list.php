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
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="aggregator_account_list"
                               data-controller="AggregatorAccountListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500, 1000">
                            <thead>
                            <tr>
                                <th data-slug="marketplaceType"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tipo Aggregatore</th>
                                <th data-slug="marketplace"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Aggregatore</th>
                                <th data-slug="marketplaceAccount"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center">Account</th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center">isActive</th>
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
    <bs-toolbar-group data-group-label="Estrai">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Azioni">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-sitemap"
            data-button="true"
            data-permission="/admin/product/edit&&allShops"
            data-rel="tooltip"
            data-placement="bottom"
            data-class="btn btn-default"
            data-title="statistiche categorie"
            data-event="bs.marketplace.category.href"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-pencil"
            data-button="true"
            data-permission="/admin/product/edit&&allShops"
            data-rel="tooltip"
            data-placement="bottom"
            data-class="btn btn-default"
            data-title="Configurazioni"
            data-event="bs.marketplace-account.config.href"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi un nuovo aggregatore"
                data-placement="bottom"
                data-href="/blueseal/marketplace/account-inserisci"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>