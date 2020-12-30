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
                               data-datatable-name="productshare_hasshopdestination_list"
                               data-controller="ProductShareHasShopDestinationListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-length="200">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                          data-searchable="true"
                                          data-orderable="true"
                                          class="center">Marketplace Account
                                </th>
                                <th data-slug="marketplaceAccountName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">MarketPlace Account
                                </th>
                                <th data-slug="marketplaceName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">MarketPlace
                                </th>
                                <th data-slug="img"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Immagine
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
    <bs-toolbar-group data-group-label="Gestione Regole">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus-circle"
                data-permission="/admin/marketing"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Nuova Regola"
                data-placement="bottom"
                data-href="/blueseal/marketplace/shop-paralleli/aggiungi"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.delete.productsharehasnewshopdestination"
        ></bs-toolbar-button>

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>