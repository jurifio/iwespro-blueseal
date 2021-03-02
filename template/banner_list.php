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
                               data-datatable-name="banner_list"
                               data-controller="BannerListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">id</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Nome</th>
                                <th data-slug="textHtml"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Banner</th>
                                <th data-slug="couponType"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Tipo coupon</th>
                                <th data-slug="click"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Click</th>
                                <th data-slug="startDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Valido da</th>
                                <th data-slug="endDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Valido fino a</th>
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
    <bs-toolbar-group data-group-label="Gestione evento coupon">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/marketing"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo evento coupon"
            data-placement="bottom"
            data-href="/blueseal/eventocoupon/aggiungi"></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/marketing"
            data-event="bs.couponevent.del"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina evento coupon"
            data-placement="bottom"
            data-toggle="modal"></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>