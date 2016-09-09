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
                        <table class="table table-striped responsive" width="100%" data-datatable-name="brand_list"
                               data-datatable-name="product_slim_list"
                               data-column-filter="true"
                               data-controller="BrandListAjaxController"
                               data-inner-setup="true"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               >
                            <thead>
                            <tr>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome</th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione</th>
                                <?php if(count($shops)> 1): ?>
                                <th data-slug="shop"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Shop</th>
                                <?php endif; ?>
                                <th data-slug="prodotti"
                                    data-searchable="true"
                                    data-orderable="true" class="center">N° Prodotti</th>
                                <th data-slug="quantita"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Quantità</th>
                                <th data-slug="valore_al_costo"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Valore Costo</th>
                                <th data-slug="valore_al_prezzo"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Valore al Prezzo</th>
                                <?php if($app->getUser()->hasPermission('allShops')): ?>
                                    <th data-slug="incasso_friend"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Stagione</th>
                                <?php else: ?>
                                    <th data-slug="incasso_picky"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Incasso</th>
                                <?php endif; ?>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
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
            data-target="#bsModal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>