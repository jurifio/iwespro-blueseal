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
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php"; ?>
    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>
            <div class="container container-fluid form-container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Testata Movimento</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <form id="form-project" enctype="multipart/form-data" role="form" method="post"
                                      autocomplete="off">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default">
                                                <label for="mag-movementDate" class="fade">Data
                                                    <input type="date" name="mag-movementDate"
                                                           class="form-control mag-movementDateInput"
                                                           id="movement-date"
                                                           value="<?php echo date('Y-m-d') ?>" required="required">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="storehouseOperationCauseId">Causali</label>
                                                <select class="full-width"
                                                        placeholder="Seleziona la causale"
                                                        data-init-plugin="selectize"
                                                        id="storehouseOperationCauseId" required>
                                                    <option></option>
                                                    <?php foreach ($causes as $storehouseOperationCause): ?>
                                                        <option
                                                            value="<?php echo $storehouseOperationCause->id ?>"><?php echo $storehouseOperationCause->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if ($shops->count() == 1) : ?>
                                            <input hidden id="shopId" value="<?php echo $shops->getFirst()->id ?>">
                                        <?php else: ?>
                                            <div class="col-md-4">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="shopId">Shop</label>
                                                    <select class="full-width"
                                                            placeholder="Seleziona lo shop"
                                                            data-init-plugin="selectize"
                                                            id="shopId" required>
                                                        <option></option>
                                                        <?php foreach ($shops as $shop): ?>
                                                            <option
                                                                value="<?php echo $shop->id ?>"><?php echo $shop->title ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <div
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Ricerca Codice</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <label for="barcode" class="fade">Barcode</label>
                                            <input id="barcode" disabled="disabled" autocomplete="off"
                                                   class="form-control" type="text"
                                                   maxlength="10" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Prodotti movimentati</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped" id="linesList">
                                            <thead>
                                            <tr>
                                                <th>barcode</th>
                                                <th>descrizione</th>
                                                <th>qty</th>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-floppy-o"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.storehouse.operation.fast.save"
            data-title="Salva"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>