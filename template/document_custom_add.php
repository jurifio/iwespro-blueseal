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
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Testata Fattura</h5>
                                </div>
                                <div id="invoiceHeadContainer" class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shopRecipientId">Indirizzo Fatturazione</label>
                                                <select id="shopRecipientId" class="full-width selectpicker"
                                                        placeholder="Seleziona un indirizzo" name="shopRecipientId"
                                                        required="required"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default">
                                                <label for="number">Numero</label>
                                                <input id="number" class="form-control"
                                                       placeholder="Numero Fattura" name="number" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default">
                                                <label for="totalWithVat">Totale con iva</label>
                                                <input id="totalWithVat" class="form-control"
                                                       placeholder="Totale" type="number" step="0.01"
                                                       name="totalWithVat" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="invoiceTypeId">Tipo Fattura</label>
                                                <select id="invoiceTypeId" class="form-control"
                                                        placeholder="Seleziona tipo fattura" required="required"
                                                        name="invoiceTypeId"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default">
                                                <label for="date">Data Fattura</label>
                                                <input id="date" class="form-control"
                                                       type="date" placeholder="Pagamento" name="date" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default">
                                                <label for="paymentExpectedDate">Data Pagamento</label>
                                                <input id="paymentExpectedDate" class="form-control" required="required"
                                                       placeholder="Pagamento" type="date" name="paymentExpectedDate">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                <label for="invoiceBin">File</label>
                                                <input id="invoiceBin" class="form-control" required="required"
                                                       placeholder="Pagamento" type="file" name="invoiceBin">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group form-group-default">
                                                <label for="note">Note</label>
                                                <input id="note" class="form-control"
                                                       type="text" placeholder="Note" name="note">
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
                                    <h5 class="m-t-10" style="display: inline-block">Righe Fattura</h5> <a href="#" id="addInvoiceLine" style="display: inline-block"><i class="fa fa-plus-circle fa-2x" aria-hidden="true"></i></a>
                                </div>
                                <div id="invoiceLineContainer" class="panel-body clearfix">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione ordini">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="/admin/order/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newInvoice.save"
                data-title="Inserisci Fattura"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>