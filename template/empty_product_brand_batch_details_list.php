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
                               data-datatable-name="empty_product_brand_batch_list"
                               data-controller="EmptyProductBrandBatchDetailsListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500"
                               data-productbatchid="<?php echo $productBatchId ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="pBrandId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand Id</th>
                                <th data-slug="slug"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Slug</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Name</th>
                                <th data-slug="description"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Descrizione</th>
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

    <bs-toolbar-group data-group-label="Elimina prodotti">
        <bs-toolbar-button
                data-remote="bs.delete.product.brand.from.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>