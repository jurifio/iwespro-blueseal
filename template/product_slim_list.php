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
                               data-datatable-name="product_slim_list"
                               data-column-filter="true"
                               data-controller="ProductSlimListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-lenght-menu="100, 200, 500">
                            <thead>
                                <tr>
                                    <th data-slug="code"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Codice</th>
                                    <th data-slug="image"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Immagine </th>
                                    <th data-slug="brand"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Brand</th>
                                    <th data-slug="cpf"
                                        data-searchable="true"
                                        data-orderable="true" class="center">CPF</th>
                                    <th data-slug="externalId"
                                        data-searchable="true"
                                        data-orderable="true" class="center">ID Orig.</th>
                                    <th data-slug="categories"
                                        data-searchable="false"
                                        data-orderable="false" class="center">Categorie</th>
                                    <th data-slug="season"
                                        data-searchable="true"
                                        data-orderable="true"
                                        data-default-order="desc" class="center">Stagione</th>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo prodotto"
            data-placement="bottom"
            data-href="/blueseal/prodotti/aggiungi"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-cloud-upload"
            data-permission="/admin/product/publish&&allShops"
            data-event="bs.pub.product"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Pubblica prodotti"
            data-placement="bottom"
            data-toggle="modal"
            data-target="#bsModal"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-qrcode"
            data-permission="/admin/product/list"
            data-event="bs.print.aztec"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Stampa aztec"
            data-placement="bottom"
            >
        </bs-toolbar-button><bs-toolbar-button
            data-tag="a"
            data-icon="fa-barcode"
            data-permission="/admin/product/list"
            data-event="bs.print.barcode"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Stampa barcode"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-tag"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.product.tag"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Tagga prodotti"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-clone"
            data-permission="/admin/product/add&&allShops"
            data-event="bs.dupe.product"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Duplica prodotto"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-archive"
            data-permission="/admin/product/mag"
            data-event="bs.add.sku"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Movimenta"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-camera-retro"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.manage.photo"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Gestisci foto"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/product/delete&&allShops"
            data-event="bs.del.product"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina prodotto"
            data-placement="bottom"
            data-toggle="modal"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-eye"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.manage.changeStatus"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Cambia Status ai prodotti selezionati"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-calendar"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.manage.changeSeason"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Cambia Stagione ai prodotti selezionati"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-sitemap"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.category.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Cambia Categoria ai prodotti selezionati"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button><bs-toolbar-button
            data-tag="a"
            data-icon="fa-usd"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.sales.set"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Metti in saldo i prodotti Selezionati"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button><bs-toolbar-button
            data-tag="a"
            data-icon="fa-percent"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.sales.price"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Cambia prezzi ai prodotti selezionati"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-tasks"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.product.mergedetails"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Copia dettagli"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-magnet"
            data-permission="/admin/product/edit&&allShops"
            data-event="bs.product.mergenames"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Copia i nomi dei prodotti"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>