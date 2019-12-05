<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'],$page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <!--<script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>-->
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=z3tiwzxrspg36g21tiusdfsqt9f27isw6547l88aw19e0qej"></script>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">


            <div class="container-fluid container-fixed-lg bg-white">
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Aggiungi un Acquisto</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="invoice">Fattura </label>
                                                <input id="invoice" class="form-control" type="text"
                                                       placeholder="inserisci il numero Fattura di Acquisto"
                                                       name="invoice"
                                                       required="required">
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="dateMovement">Data Movimento</label>
                                                <input type="datetime-local" id="dateMovement"
                                                       placeholder="Inserisci la data Del Movimento"
                                                       name="dateMovement">
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="gainPlanId">Fattura di Vendita Collegata </label>
                                                <select id="gainPlanId" class="form-control"
                                                        placeholder="Seleziona il documento di Vendita Collegato"
                                                        name="gainPlanId"
                                                        required="required"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isActive">Attivo </label>
                                            <input id="isActive" class="form-control" type="checkbox"
                                                   placeholder="seleziona se è attivo" name="isActive">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="fornitureName">Fornitore</label>
                                            <input id="fornitureName" class="form-control"
                                                   placeholder="inserisci il Fornitore" name="fornitureName"
                                                   required="required">
                                            <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="serviceName">Servizio</label>
                                            <input id="serviceName" class="form-control"
                                                   placeholder="inserisci il numero Fattura" name="serviceName"
                                                   required="required">
                                            <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="amount">Importo</label>
                                            <input id="amount" class="form-control"
                                                   placeholder="inseriri il numero Fattura" name="amount"
                                                   required="required">
                                            <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="iva">iva</label>
                                            <select class="full-width"
                                                    placeholder="Seleziona Aliquota"
                                                    data-init-plugin="selectize" title="" name="iva" id="iva"
                                                    required>
                                                <option></option>
                                                <option value="22">iva 22%</option>
                                                <option value="1">Esente Iva</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>

    <?php include "parts/footer.php"; ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Salva il Movimento">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-save"
                data-permission="allShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.gainplan.passivemovement.save"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>