<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
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
            <div class="container form-container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading price-manage-heading">
                                <h3 style="float:left">codice prodotto</h3><button class="btn btn-default addRow" style="float: right;">Inserisci nuovo Shop</button>
                                <br class="clear: both" />
                            </div>
                            <div class="panel-body">
                                <form class="form-prices-per-product">
                                    <input type="hidden" name="id" value="" />
                                    <input type="hidden" name="productVariantId" value="" />
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="row row-price">
                                                <div class="form-group col-md-2">
                                                    <label for="">Shop</label>
                                                    <select class="form-control select-shop" type="text" name="" value="" required />
                                                        <option default value="">-Seleziona uno shop-</option>
                                                    <?php foreach($shops as $s) : ?>
                                                        <option value="<?php echo $s->id ?>"><?php echo $s->title ?></option>
                                                    <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="">Id Origine</label>
                                                    <input class="form-control extId" type="text" name="extId" value="" />
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="">Costo</label>
                                                    <input class="form-control value" type="text" name="value" value="" required/>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="">Prezzo</label>
                                                    <input class="form-control price" type="text" name="price" value="" required/>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label for="">Prezzo in saldo</label>
                                                    <input class="form-control salePrice" type="text" name="salePrice" value="" required/>
                                                </div>
                                                <div class="col-md-2">
                                                    <button class="btn btn-success removeRow" style="float: right;">x</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"; ?>
</div>
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
            data-event="bs.prices.edit"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>