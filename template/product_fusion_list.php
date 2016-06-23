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
                               data-datatable-name="product_list"
                               data-column-filter="true"
                               data-controller="ProductListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                                <tr>
                                    <th class="center">Codice</th>
                                    <th class="center">Shop</th>
                                    <th class="center">Gruppo Colore</th>
                                    <th class="center">Stagione</th>
                                    <th class="center">ID Orig.</th>
                                    <th class="center">CPF</th>
                                    <th class="center">Immagine </th>
                                    <th class="center">Brand</th>
                                    <th class="center">Categorie</th>
                                    <th class="center">Tags</th>
                                    <th class="center">Stato</th>
                                    <th class="center">Disponibile?</th>
                                    <th class="center">Creazione</th>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo prodotto"
            data-placement="bottom"
            data-href="<?php echo $aggiungi; ?>"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-cloud-upload"
            data-permission="/admin/product/publish"
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
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-tag"
            data-permission="/admin/product/edit"
            data-event="bs.product.tag"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Tagga prodotti"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-clone"
            data-permission="/admin/product/add"
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
            data-permission="/admin/product/edit"
            data-event="bs.manage.photo"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Gestisci foto"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/product/delete"
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
            data-permission="/admin/product/edit"
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
            data-permission="/admin/product/edit"
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
            data-permission="/admin/product/edit"
            data-event="bs.category.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Cambia Stagione ai prodotti selezionati"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-magnet"
            data-permission="/admin/product/edit"
            data-event="bs.product.merge"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Fondi due prodotti"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>

    <bs-toolbar-group data-group-label="Roulette">
        <bs-toolbar-select
            data-tag="select"
            data-icon="fa-random"
            data-button="true"
            data-permission="/admin/product/add"
            data-rel="tooltip"
            data-placement="bottom"
            data-class="btn btn-default"
            data-title="Roulette prodotti"
            data-event="bs.roulette.add"
            data-options='<?php echo json_encode($roulette); ?>'
            ></bs-toolbar-select>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>