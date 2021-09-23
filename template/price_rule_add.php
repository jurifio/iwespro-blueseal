<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
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
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5>Aggiungi Regola Listino</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                              autocomplete="off">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                <label for="name">Nome Regola</label>
                                                ​<input type="text" class="form-control" id="name" name="name"
                                                        required="required"
                                                        placeholder="Inserisci il nome del listino"
                                                        value=""/>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shopId">Shop del Listino</label>
                                                <select id="shopId" name="shopId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                    <option value="">seleziona</option>
                                                    <?php
                                                    foreach ($shops as $shop) {
                                                        echo '<option value="' . $shop->id . '">' . $shop->name . '</option>';
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                <label for="dateStart">Valido da</label>
                                                <input type="datetime-local" class="form-control" id="dateStart"
                                                       name="dateStart"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                <label for="dateEnd">Valido fino a</label>
                                                <input type="datetime-local" class="form-control" id="dateEnd"
                                                       name="dateEnd"
                                                       value=""/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="typeVariation"> Tipo Variazione su Prezzo Base</label>
                                                ​<select id="typeVariation" name="typeVariation"
                                                         class="full-width selectpicker"
                                                         placeholder="Seleziona la Lista"
                                                         data-init-plugin="selectize">
                                                    <option value="1">Sconto</option>
                                                    <option value="2">Maggiorazione</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="variation">Variazione</label>
                                                <input type="text" id="variation" name="variation" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="typeVariationSale">Operazioni tipo di Saldo</label>
                                                ​<select id="typeVariationSale" name="typeVariationSale"
                                                         class="full-width selectpicker"
                                                         placeholder="Seleziona la Lista"
                                                         data-init-plugin="selectize">
                                                    <option value="1">No</option>
                                                    <option value="2">Percentuale</option>
                                                    <option value="3">Prezzo Fisso Prodotti Stock</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="variationSale">Valore</label>
                                                <input type="text" id="variationSale" name="variationSale" value=""/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group form-group-default">
                                                <label class="radio-inline"><input type="radio" id="optRadioRound"
                                                                                   name="optRadioRound" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optRadioRound"
                                                                                   name="optRadioRound"
                                                                                   value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optRadioRound"
                                                                                   checked name="optRadioRound"
                                                                                   value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optRadioRound"
                                                                                   name="optRadioRound" value="+1">Arrotonda
                                                    eccesso a Euro 1,0</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="radio-inline"><input type="radio" id="typeAssignBrand"
                                                                       name="typeAssignBrand" value="1">Assegnazione
                                        Automatica tutti i Brand</label>
                                    <label class="radio-inline"><input type="radio" id="typeAssignBrand"
                                                                       name="typeAssignBrand" value="2">Assegnazione
                                        Per Brand con opzioni di esclusione</label>
                                </div>
                            </div>
                            <div id="rawRuleBrand" class="hide">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="brandId">Seleziona i Brand da escludere
                                            </label>
                                            <select id="brandId"
                                                    name="brandId"
                                                    class="full-width selectpicker"
                                                    placeholder="Selezione dei Brand"
                                                    data-init-plugin="selectize">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <input type="text" id="brandsPar" name="brandsPar"
                                                   value=" "/>
                                        </div>
                                    </div>
                                </div>
                                <div id="appendBrandsPar">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="radio-inline"><input type="radio" id="typeAssignSeason"
                                                                       name="typeAssignSeason" value="1">Assegnazione
                                        Automatica tutte le Stagioni</label>
                                    <label class="radio-inline"><input type="radio" id="typeAssignSeason"
                                                                       name="typeAssignSeason" value="2">Assegnazione
                                        Per Stagione con opzioni di esclusione</label>
                                </div>
                            </div>
                            <div id="rawRuleSeason" class="hide">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="seasonId">Seleziona la Stagione da escludere
                                            </label>
                                            <select id="seasonId"
                                                    name="seasonId"
                                                    class="full-width selectpicker"
                                                    placeholder="Selezione delle Stagioni"
                                                    data-init-plugin="selectize">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group form-group-default">
                                            <input type="text" id="seasonsPar" name="seasonsPar"
                                                   value=" "/>
                                        </div>
                                    </div>
                                </div>
                                <div id="appendSeasonsPar">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"; ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.price.rule.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>