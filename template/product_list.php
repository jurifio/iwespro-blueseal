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
                                    <!--<th class="center">Gruppo Taglie</th>-->
                                    <th class="center">Dettagli</th>
                                    <!--<th class="center">Immagine </th>-->
                                    <th class="center">Brand</th>
                                    <th class="center">Categorie</th>
                                    <th class="center">Tags</th>
                                    <th class="center">Stato</th>
                                    <th class="center">M.Up</th>
                                    <th class="center">Disp.</th>
                                    <th class="center">SCA</th>
                                    <th class="center dataFilterType">Creazione</th>
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
            data-remote="btn.href.add.product"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.publish.products"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.print.aztec"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.tag.change"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.dupe"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.sku.manage"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.photo.manage"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.photo.download"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.delete"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.status.change"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.season.change"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.category.change"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.details.merge"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-magnet"
            data-permission="/admin/product/edit"
            data-event="bs.product.mergenames"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Copia i nomi dei prodotti"
            data-placement="bottom"
            data-toggle="modal"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.sizeGroup.change"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.model.createByProduct"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.PriceEditForAllShop"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.model.insertIntoProducts"
        ></bs-toolbar-button>
        </bs-toolbar-group>
        <bs-toolbar-group data-group-label="Gestione prezzi">
            <bs-toolbar-button
                data-remote="bs.product.sales.set"
                ></bs-toolbar-button>
            <bs-toolbar-button
                data-remote="bs.product.sales.price.change"
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