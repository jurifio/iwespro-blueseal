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
                               data-datatable-name="coupontype_list"
                               data-controller="CouponTypeListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true">
                            <thead>
                            <tr>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Nome</th>
                                <th data-slug="amount"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Valore</th>
                                <th data-slug="validity"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Validità</th>
                                <th data-slug="validForCartTotal"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Minimo spesa</th>
                                <th data-slug="hasFreeShipping"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Spedizione Gratuita</th>
                                <th data-slug="hasFreeReturn"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Reso Gratuito</th>
                                <th data-slug="tags"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tags</th>
                                <th data-slug="campaignId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Campagna Associata</th>
                                <th data-slug="remoteShopId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shop di Destinazione</th>

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
    <bs-toolbar-group data-group-label="Gestione tipo coupon">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/marketing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo tipo coupon"
            data-placement="bottom"
            data-href="/blueseal/tipocoupon/aggiungi"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>