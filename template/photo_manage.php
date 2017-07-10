<!DOCTYPE html>
<?php
/** @var $app bamboo\core\theming\CRestrictedAccessWidgetHelper */
/** @var $productEdit bamboo\app\domain\entities\CProduct */
?>
<html>
<head>
    <?php include "parts/head.php"; ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
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
                            <p class="m-t-10">Informazioni Prodotto</p>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label>Codice Prodotto Fornitore</label>
                                        <input style="color: black" disabled autocomplete="off" type="text"
                                               class="form-control"
                                               value="<?php echo isset($productEdit->itemno) ? $productEdit->itemno : "" ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label>Codice variante</label>
                                        <input style="color: black" autocomplete="off" type="text" class="form-control"
                                               value="<?php echo $productEdit->productVariant->name ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label>Brand</label>
                                        <input style="color: black" autocomplete="off" type="text" class="form-control"
                                               value="<?php echo $productEdit->productBrand->name ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div id="selectable">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <form class="form"
                                  action="#"
                                  id="photoOrderForm"
                                  method="POST" autocomplete="off"
                                  data-url="<?php echo "" ?>">
                                <input type="hidden" name="id" value="<?php echo $productEdit->id ?>">
                                <input type="hidden" name="productVariantId"
                                       value="<?php echo $productEdit->productVariantId ?>">

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
    <?php include "parts/footer.php" ?>
</div>

<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Foto">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-image-o"
            data-permission="/admin/product/edit"
            data-rel="tooltip"
            data-event="bs.add.photo"
            data-placement="bottom"
            data-class="btn btn-default"
            data-title="Aggiungi Foto"
            data-toggle="modal"

            data-href="#"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>
