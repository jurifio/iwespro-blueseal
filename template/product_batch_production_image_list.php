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
                               data-datatable-name="product_batch_production_image_list"
                               data-controller="ProductBatchHasProductionImageListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500"
                               data-productbatchid="<?php echo $productBatchId ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="imageName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome Immagine</th>
                                <th data-slug="dummy"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Immagine</th>
                                <th data-slug="shopId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop di Provenienza</th>
                                <th data-slug="stepName"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Stato Lavorazione</th>
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
    <bs-toolbar-group data-group-label="Termina lavorazione sul prodotto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-step-forward"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.end.work.product"
                data-title="Termina la lavorazione sui prodotti selezionati"
                data-placement="bottom"
        ></bs-toolbar-button>
        <?php if($allShops): ?>
        <bs-toolbar-button
                data-remote="bs.change.product.status.batch"
        ></bs-toolbar-button>
        <?php endif; ?>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Notifica termine lotto">
        <bs-toolbar-button
                data-remote="bs.end.product.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php if($allShops): ?>
    <bs-toolbar-group data-group-label="Elimina Immagine del Lotto">
        <bs-toolbar-button
                data-remote="bs.delete.image.from.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Inserisci nota">
        <bs-toolbar-button
                data-remote="bs.note.product.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
</bs-toolbar>
</body>
</html>