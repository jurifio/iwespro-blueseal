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
                               data-datatable-name="product_brand_batch_list"
                               data-controller="ProductBrandBatchDetailsListAjaxController"
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
                                    data-orderable="true" class="center">BrandId</th>
                                <th data-slug="slug"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Slug</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Name</th>
                                <th data-slug="description"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Descrizione</th>
                                <th data-slug="stepName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stato</th>
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

    <bs-toolbar-group data-group-label="Termina lavorazione">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-step-forward"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.end.work.product.brand"
                data-title="Termina la lavorazione sui prodotti selezionati"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.end.product.batch"
        ></bs-toolbar-button>
        <?php if($allShops) : ?>
        <bs-toolbar-button
                data-remote="bs.change.product.status.batch"
        ></bs-toolbar-button>
        <?php endif;?>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>