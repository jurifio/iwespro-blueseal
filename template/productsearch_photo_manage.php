<!DOCTYPE html>
<?php
/** @var $app bamboo\core\theming\CRestrictedAccessWidgetHelper */
/** @var $productEdit bamboo\app\domain\entities\CProduct */
?>
<html>
<head>
    <?php include "parts/head.php"; ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
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
                        <div class="row">
                            <p class="m-t-10">Ricerca Prodotto</p>

                            <div class="row">
                                <div class="col-md-2">
                                    <div>Prodotto Trovato: <span id="dbr"></span></div>
                                    <div class="select">
                                        <label for="videoSource">Video source: </label><select
                                                id="videoSource"></select>
                                    </div>
                                    <button id="go" class="btn btn-primary">Leggi il barcode</button>
                                </div>
                                <div class="col-md-4">
                                    <video muted autoplay id="video" playsinline="true"  width="240"></video>

                                </div>
                                <div class="col-md-4">
                                    <canvas id="pcCanvas" width="240" height="320"
                                            style="display: none; float: bottom;"></canvas>
                                    <canvas id="mobileCanvas" width="240" height="320"
                                            style="display: none; float: bottom;"></canvas>
                                    <div id="codice"></div>
                                </div>
                                <script async src="/assets/js/zxing.js"></script>
                                <script src="/assets/js/video.js"></script>
                            </div>
                        </div>
                        <div class="row">
                            <p class="m-t-10">Informazioni Prodotto</p>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label>Codice Prodotto Fornitore</label>
                                        <input id="resultCPF" style="color: black" disabled autocomplete="off"
                                               type="text"
                                               class="form-control"
                                               value="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label>Codice variante</label>
                                        <input id="resultVariante" style="color: black" autocomplete="off" type="text"
                                               class="form-control"
                                               value="" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label>Brand</label>
                                        <input id="resultBrand" style="color: black" autocomplete="off" type="text"
                                               class="form-control"
                                               value="" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="selectable">

                            </div>
                        </div>
                    </div>
                    <div id="hiddenDiv" class="hide">
                        <div class="row">
                            <div class="col-sm-12">
                                <form class="form"
                                      action="#"
                                      id="photoOrderForm"
                                      method="POST" autocomplete="off"
                                      data-url="<?php echo "" ?>">
                                    <input type="hidden" id="id" name="id" value="">
                                    <input type="hidden" id="productVariantId" name="productVariantId"
                                           value="">

                                    <div class="row">
                                        <button class="btn btn-success" type="submit">Salva</button>
                                        <button class="btn btn-default"><i class="pg-close"></i> Cancella</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>

<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Foto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-camera-retro"
                data-permission="/admin/product/edit"
                data-rel="tooltip"
                data-event="bs.take.photo"
                data-placement="bottom"
                data-class="btn btn-default"
                data-title="Scatta Foto"
                data-toggle="modal"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>
