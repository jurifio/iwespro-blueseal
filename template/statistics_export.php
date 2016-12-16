<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header" data-shops=""><div class="product-code">
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
            <div class="container-fluid">
                <div class="container-fluid container-fixed-lg bg-white">
                    <div class="panel panel-transparent">
                        <div class="panel-body">
                            <h2>Esportazione Statistiche</h2>
                            <p class="export-message">
                                <?php
                                if ($filename):
                                    $formattedDate = $d . "/" . $m . "/" . $Y;
                                    $formattedTime = $H . ":" . $i . ":" . $s;
                                ?>
                                I dati disponibili sono stati elaborati fino al: <span id="export-max-date"><?php echo $formattedDate . " " . $formattedTime; ?></span>. <a href="/blueseal/statistiche/file">Scaricali</a>
                                <?php
                                else:
                                ?>
                                Al momento non risultano file disponibili per il download.
                                <?php
                                endif;
                                ?>
                            </p>
                            <p>
                                <button class="btn btn-default create-statistics">Rielabora i dati</button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php include "parts/footer.php"; ?>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.product.edit"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-qrcode"
            data-permission="/admin/product/add"
            data-event="bs.print.aztec"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Codice Aztec"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-image"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-event="bs.dummy.edit"
            data-rel="tooltip"
            data-title="Dummy picture"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-sitemap"
            data-permission="/admin/product/add"
            data-event="bs.category.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Categorie"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-tag"
            data-permission="/admin/product/add"
            data-event="bs.tag.edit"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Tag"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione dettagli">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-eraser"
            data-permission="/admin/product/edit"
            data-event="bs.det.erase"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Vuota i dettagli"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.product.details.new"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-files-o"
            data-permission="/admin/product/edit"
            data-event="bs.details.model.assign"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Carica i dettagli da modello"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file"
            data-permission="/admin/product/edit"
            data-event="bs.details.product.assign"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Carica i dettagli da prodotto"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Movimenti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-exchange"
            data-permission="/admin/product/edit"
            data-event="bs.details.mag.move"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Quantità e movimenti"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione Nomi Prodotti">
        <bs-toolbar-button
            data-remote="bs.product.name.insert"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Aiuto">
        <bs-toolbar-button
            data-remote="btn.href.smart_product.guide"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>