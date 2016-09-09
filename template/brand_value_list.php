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
                               data-datatable-name="brand_value_list"
                               data-column-filter="true"
                               data-controller="BrandValueListAjaxController"
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
                                    data-orderable="true" class="center"  data-default-order="desc" >N° Prodotti</th>
                                <th data-slug="quantita"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Quantità</th>
                                <th data-slug="valore_al_costo"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Valore Costo</th>
                                <th data-slug="valore_al_prezzo"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Valore al Prezzo</th>
                                <?php if($app->user()->hasPermission('allShops')): ?>
                                    <th data-slug="incasso_picky"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Incasso</th>
                                <?php else: ?>
                                    <th data-slug="incasso_friend"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Incasso</th>
                                <?php endif; ?>
                                <th data-slug="venduto"
                                    data-searchable="false"
                                    data-orderable="true" class="center">Quantità Venduta</th>
                                <th data-slug="cancellato"
                                    data-searchable="false"
                                    data-orderable="true" class="center">Quantità Cancellata</th>
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

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>