<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
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
                        <table class="table table-striped responsive" width="100%" data-datatable-name="brand_list"
                               data-controller="BrandListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center sorting">Nome</th>
                                <th data-slug="slug"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Slug</th>
                                <th data-slug="description"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Description</th>
                                <th data-slug="logoUrl"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Logo</th>
                                <th data-slug="productCount"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">N° Prodotti</th>
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
    <bs-toolbar-group data-group-label="Gestione Brand">
        <?php  if($allShops):?>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo prodotto"
            data-placement="bottom"
            data-href="<?php echo $addUrl; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/product/delete"
            data-event="bs.brand.delete"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina prodotto"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-arrows-h"
            data-permission="/admin/product/add"
            data-event="bs.brand.modify.fason"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Assegna brand a lotto"
            data-placement="bottom"
        ></bs-toolbar-button>
        <?php endif ?>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>