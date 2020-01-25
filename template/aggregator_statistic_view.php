<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app -> getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page -> getTitle(); ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>
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
                    <div class="col-md-3">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                              autocomplete="off">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Filtri</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="marketplaceAccount">Seleziona Aggregatore</label>
                                                <select id="marketplaceAccount" name="marketplaceAccount"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="daterange">Intervallo Date</label>
                                                <input type="text" size="50" id="daterange" size="" name="daterange" value="<?php echo $dateStart.'-'.$dateEnd?>" />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="radio-inline"><input type="radio" name="optradio" value="d" checked>Giorno</label>
                                            <label class="radio-inline"><input type="radio" name="optradio" value="w">Sett</label>
                                            <label class="radio-inline"><input type="radio" name="optradio" value="m">Mese</label>
                                            <label class="radio-inline"><input type="radio" name="optradio" value="y">Anno</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="panel panel-default clearfix">
                                            <div class="panel-heading clearfix">
                                                <h5 class="m-t-10">Group Report</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="brandActive">Brand</label>
                                                    <input id="brandActive" class="form-control"
                                                           type="checkbox"
                                                           placeholder="seleziona se è attivo" name="brandActive">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productActive">Prodotto</label>
                                                    <input id="productActive" class="form-control"
                                                           type="checkbox"
                                                           placeholder="seleziona se è attivo" name="productActive">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="fancy-tree-container" id="categoryTree">
                                        </div>
                                        <div>Active node: <span id="echoActive1">-</span></div>
                                        <div>Selection: <span id="echoSelection1">-</span></div>
                                        <div>Selected root keys: <span id="echoSelectionRootKeys3">-</span></div>
                                    </div>
                                    <div class="row">
                                        <div class="panel panel-default clearfix">
                                            <div class="panel-heading clearfix">
                                                <h5 class="m-t-10">DeviceType</h5>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="desktopActive">Desktop</label>
                                                    <input id="desktopActive" class="form-control"
                                                           type="checkbox"
                                                           placeholder="seleziona se è attivo" name="desktopActive">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="mobileActive">Mobile</label>
                                                    <input id="mobileActive" class="form-control"
                                                           type="checkbox"
                                                           placeholder="seleziona se è attivo" name="mobileActive">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button type="button" id="generate" name="generate" class="btn btn-primary">
                                                Genera
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-9">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Grafici</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" id="firstGraph">
                                        grafico uno
                                    </div>
                                    <div class="col-md-6" id="secondGraph">
                                        grafico due
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2" id="productPublish">
                                        prodotti Pubblicati
                                    </div>
                                    <div class="col-md-2" id="click">
                                        Click Ricevuti
                                    </div>
                                    <div class="col-md-2" id="CpcFornitore">
                                        Importo Spese CpcFornitore
                                    </div>
                                    <div class="col-md-2" id="CpcCliente">
                                        Importo Spese Cpc Cliente
                                    </div>
                                    <div class="col-md-2" id="qtySell">
                                        Quantità venduta
                                    </div>
                                    <div class="col-md-2" id="qtyValue">
                                        Valore vendite
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2" id="rateConversionQty">
                                        Tasso di Conversione Quantità
                                    </div>
                                    <div class="col-md-2" id="click">
                                        Click Ricevuti
                                    </div>
                                    <div class="col-md-2" id="rateConversionCpcFornitore">
                                        Tasso Conversione Cpc Fornitore
                                    </div>
                                    <div class="col-md-2" id="rateConversioneCpcCliente">
                                        Tasso conversione Cpc Cliente
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" id="columnresult">
                                        colonna risultati
                                    </div>
                                </div>
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
    <bs-toolbar-group data-group-label="Gestione Statistiche Aggregatori">

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>