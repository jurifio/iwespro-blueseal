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
                <div class="row">
                    <div class="tab">
                        <div class="col-md-12">
                            <button class="tablinks" onclick="openTab(event, 'insertMarketPlace')">Crea Tag di
                                Pubblicazione
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'fullPriceList')">Listino Pieno</button>
                            <button class="tablinks" onclick="openTab(event, 'salePriceList')">Listino In Saldo</button>
                            <button class="tablinks" onclick="openTab(event, 'ruleNameProduct')">Modifica Nome
                                Prodotto
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'ruleAttribution')">Regole di
                                Attribuzione
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'ruleAttributionParallel')">Regole di
                                Attribuzione Prodotti Paralleli
                            </button>
                        </div>
                    </div>
                </div>
                <div id="insertMarketPlace" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Informazioni di base</h5>
                                        <input type="hidden" id="marketplaceAccountId" name="marketplaceAccountId"
                                               value="<?php echo $marketplaceAccount->id ?>"/>
                                        <input type="hidden" id="marketplaceSelectedId" id="marketplaceSelectedId"
                                               value="<?php echo $marketplaceAccount->config['marketplaceHasShopId'] ?>"/>
                                        <input type="hidden" id="langSelectId" id="langSelectId"
                                               value="<?php echo $langSelectId ?>"/>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="marketplace_account_name">Nome</label>
                                                <input id="marketplace_account_name" autocomplete="off" type="text"
                                                       class="form-control" name="marketplace_account_name"
                                                       value="<?php echo $marketplaceAccount->name ?>"
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="lang">Selettore Lingua
                                                </label>
                                                <select id="lang"
                                                        name="lang"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione la Lingua "
                                                        data-init-plugin="selectize">
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="slug">Slug</label>
                                                <input id="slug" autocomplete="off" type="text"
                                                       class="form-control" name="slug"
                                                       value="<?php echo (isset($marketplaceAccount->config['slug'])) ? $marketplaceAccount->config['slug'] : ''; ?>"
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-primary" name="uploadLogo"
                                                    id="uploadLogo">carica Logo
                                            </button>
                                            <input id="logoFile" type="hidden" value="<?php echo (isset($marketplaceAccount->config['logoFile'])) ? $marketplaceAccount->config['logoFile'] : ''; ?>"/>
                                            <div id="returnFileLogo"><?php echo (isset($marketplaceAccount->config['logoFile'])) ? '<img width="50" src="' . $marketplaceAccount->config['logoFile'] . '"/>' : ''; ?></div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isActive">Seleziona se Attivo
                                                </label>
                                                <select id="isActive"
                                                        name="isActive"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione se attivo"
                                                        data-init-plugin="selectize">
                                                    <option value=""></option>
                                                    <?php if ($marketplaceAccount->isActive == 1) {
                                                        echo '<option selected value="1">Si</option>';
                                                        echo '<option value="0">No</option>';
                                                    } else {
                                                        echo '<option  value="1">Si</option>';
                                                        echo '<option  selected value="0">No</option>';
                                                    } ?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="marketplaceHasShopId">Seleziona il MarketPlace
                                                </label>
                                                <select id="marketplaceHasShopId"
                                                        name="marketplaceHasShopId"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione il marketplace"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group form-group-default required">
                                                <label for="nameAdminister">Account Manager</label>
                                                <input id="nameAdminister" autocomplete="off" type="text"
                                                       class="form-control" name="nameAdminister"
                                                       value="<?php echo (isset($marketplaceAccount->config['nameAdminister'])) ? $marketplaceAccount->config['nameAdminister'] : ''; ?>"
                                                required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default required">
                                                <label for="emailNotify">Email Notifica </label>
                                                <input id="emailNotify" autocomplete="off" type="text"
                                                       class="form-control" name="emailNotify"
                                                       value="<?php echo (isset($marketplaceAccount->config['emailNotify'])) ? $marketplaceAccount->config['emailNotify'] : ''; ?>"
                                                       required="required"/>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="fullPriceList" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Listino Pieno</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                            if (isset($marketplaceAccount->config['activeFullPrice'])) {
                                                if ($marketplaceAccount->config['activeFullPrice'] == "1") {
                                                    echo '<label class="radio-inline"><input type="radio" id="activeFullPrice"
                                                                               name="activeFullPrice" checked value="1"
                                                >Listino Attivo no Saldi Catalogo</label>
                                            <label class="radio-inline"><input type="radio" id="activeFullPrice"
                                                                               name="activeFullPrice" value="2">listino
                                                Attivo no Saldi Personalizzati</label>';
                                                } else {
                                                    echo '<label class="radio-inline"><input type="radio" id="activeFullPrice"
                                                                               name="activeFullPrice" value="1"
                                                >Listino Attivo no Saldi Catalogo</label>
                                            <label class="radio-inline"><input type="radio" id="activeFullPrice"
                                                                               name="activeFullPrice" checked value="2">listino
                                                Attivo no Saldi Personalizzati</label>';
                                                }
                                            } else {
                                                echo '<label class="radio-inline"><input type="radio" id="activeFullPrice"
                                                                               name="activeFullPrice" value="1"
                                                >Listino Attivo no Saldi Catalogo</label>
                                            <label class="radio-inline"><input type="radio" id="activeFullPrice"
                                                                               name="activeFullPrice" value="2">listino
                                                Attivo no Saldi Personalizzati</label>';
                                            }
                                            ?>

                                        </div>
                                    </div>
                                    <div id="rawFullPrice">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php
                                                if (isset($marketplaceAccount->config['signFullPrice'])) {
                                                    if ($marketplaceAccount->config['signFullPrice'] == "1") {
                                                        echo '  <label class="radio-inline"><input type="radio" id="signFullPrice"
                                                                                   name="signFullPrice" value="1"
                                                                                   checked>Scostamento rispetto al
                                                    listino attivo catalogo Diminuzione (-)</label>
                                                <label class="radio-inline"><input type="radio" id="signFullPrice"
                                                                                   name="signFullPrice"  value="2">Scostamento
                                                    rispetto al listino attivo catalogo Maggiorazione(+)</label>';
                                                    } else {
                                                        echo '  <label class="radio-inline"><input type="radio" id="signFullPrice"
                                                                                   name="signFullPrice" value="1"
                                                                                   >Scostamento rispetto al
                                                    listino attivo catalogo Diminuzione (-)</label>
                                                <label class="radio-inline"><input type="radio" id="signFullPrice"
                                                                                  checked name="signFullPrice" value="2">Scostamento
                                                    rispetto al listino attivo catalogo Maggiorazione(+)</label>';
                                                    }
                                                } else {
                                                    echo '  <label class="radio-inline"><input type="radio" id="signFullPrice"
                                                                                   name="signFullPrice" value="1"
                                                                                   >Scostamento rispetto al
                                                    listino attivo catalogo Diminuzione (-)</label>
                                                <label class="radio-inline"><input type="radio" id="signFullPrice"
                                                                                   name="signFullPrice" value="2">Scostamento
                                                    rispetto al listino attivo catalogo Maggiorazione(+)</label>';
                                                }
                                                ?>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="percentFullPrice">Percentuale %</label>
                                                    <input id="percentFullPrice" autocomplete="off" type="text"
                                                           class="form-control" name="percentFullPrice"
                                                           value="<?php echo (isset($marketplaceAccount->config['percentFullPrice'])) ? $marketplaceAccount->config['percentFullPrice'] : ''; ?>"
                                                           required="required"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php
                                                if (isset($marketplaceAccount->config['optradio'])) {
                                                    switch ($marketplaceAccount->config['optradio']) {
                                                        case "-0.5":
                                                            echo '<label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" checked value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                            break;
                                                        case "+0.5":
                                                            echo '<label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" checked value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                            break;
                                                        case "+1":
                                                            echo '<label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio"  value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" checked value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                            break;
                                                        case "-1":
                                                            echo '<label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio"  value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" checked value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                            break;
                                                    }
                                                } else {
                                                    echo '<label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php
                                                if (isset($marketplaceAccount->config['optradioactive'])) {
                                                    if ($marketplaceAccount->config['optradioactive'] == "activePrice") {
                                                        echo '<label class="radio-inline"><input type="radio" id="optradioactive"
                                                                                   name="optradioactive" checked
                                                                                   value="activePrice">Scostamento
                                                    applicato su prezzo attivo
                                                </label>
                                                <label class="radio-inline"><input type="radio" id="optradioactive"
                                                                                   name="optradioactive"
                                                                                   value="fullPrice" >Scostamento
                                                    applicato su prezzo pieno
                                                </label>';
                                                    } else {
                                                        echo '<label class="radio-inline"><input type="radio" id="optradioactive"
                                                                                   name="optradioactive"
                                                                                   value="activePrice">Scostamento
                                                    applicato su prezzo attivo
                                                </label>
                                                <label class="radio-inline"><input type="radio" id="optradioactive"
                                                                                   name="optradioactive"
                                                                                   value="fullPrice" checked>Scostamento
                                                    applicato su prezzo pieno
                                                </label>';
                                                    }
                                                } else {
                                                    echo '<label class="radio-inline"><input type="radio" id="optradioactive"
                                                                                   name="optradioactive"
                                                                                   value="activePrice">Scostamento
                                                    applicato su prezzo attivo
                                                </label>
                                                <label class="radio-inline"><input type="radio" id="optradioactive"
                                                                                   name="optradioactive"
                                                                                   value="fullPrice" checked>Scostamento
                                                    applicato su prezzo pieno
                                                </label>';
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="salePriceList" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Listino Saldo</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <?php
                                            if (isset($marketplaceAccount->config['activeSalePrice'])) {
                                                if ($marketplaceAccount->config['activeSalePrice'] == "1") {
                                                    echo '<label class="radio-inline"><input type="radio"
                                                                               name="activeSalePrice"
                                                                               id="activeSalePrice" checked value="1"
                                                >Saldi Attivi come da Catalogo</label>
                                            <label class="radio-inline"><input type="radio"
                                                                               name="activeSalePrice"
                                                                               id="activeSalePrice" value="2">Saldi
                                                Attivi Personalizzati</label>';
                                                } else {
                                                    echo '<label class="radio-inline"><input type="radio"
                                                                               name="activeSalePrice"
                                                                               id="activeSalePrice" value="1"
                                                >Saldi Attivi come da Catalogo</label>
                                            <label class="radio-inline"><input type="radio"
                                                                               name="activeSalePrice" checked
                                                                               id="activeSalePrice" value="2">Saldi
                                                Attivi Personalizzati</label>';
                                                }
                                            } else {
                                                echo '<label class="radio-inline"><input type="radio"
                                                                               name="activeSalePrice" checked
                                                                               id="activeSalePrice" value="1"
                                                >Saldi Attivi come da Catalogo</label>
                                            <label class="radio-inline"><input type="radio"
                                                                               name="activeSalePrice"
                                                                               id="activeSalePrice" value="2">Saldi
                                                Attivi Personalizzati</label>';
                                            } ?>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="maxPercentSalePrice">Percentuale Massima di sconto  %</label>
                                                <input id="maxPercentSalePrice" autocomplete="off" type="text"
                                                       class="form-control" name="maxPercentSalePrice" value="<?php echo (isset($marketplaceAccount->config['maxPercentSalePrice'])) ? $marketplaceAccount->config['maxPercentSalePrice'] : ''; ?>"
                                                       required="required"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="rawSalePrice">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php if (isset($marketplaceAccount->config['signSale'])) {
                                                    if ($marketplaceAccount->config['signSale'] == "1") {
                                                        echo '<label class="radio-inline"><input type="radio" id="signSale"
                                                                                   name="signSale" value="1"
                                                                                   checked>Scostamento rispetto al
                                                    Prezzo in Saldo Diminuzione (-)</label>
                                                <label class="radio-inline"><input type="radio" id="signSale"
                                                                                   name="signSale" value="2">Scostamento
                                                    rispetto al Prezzo in Saldo Maggiorazione(+)</label>';
                                                    } else {
                                                        echo '<label class="radio-inline"><input type="radio" id="signSale"
                                                                                   name="signSale" value="1"
                                                                                   >Scostamento rispetto al
                                                    Prezzo in Saldo Diminuzione (-)</label>
                                                <label class="radio-inline"><input type="radio" id="signSale"
                                                                                   name="signSale" checked value="2">Scostamento
                                                    rispetto al Prezzo in Saldo Maggiorazione(+)</label>';
                                                    }
                                                } else {

                                                    echo '<label class="radio-inline"><input type="radio" id="signSale"
                                                                                   name="signSale" value="1"
                                                                                   checked>Scostamento rispetto al
                                                    Prezzo in Saldo Diminuzione (-)</label>
                                                <label class="radio-inline"><input type="radio" id="signSale"
                                                                                   name="signSale" value="2">Scostamento
                                                    rispetto al Prezzo in Saldo Maggiorazione(+)</label>';
                                                } ?>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="percentSalePrice">Percentuale %</label>
                                                    <input id="percentSalePrice" autocomplete="off" type="text"
                                                           class="form-control" name="percentSalePrice"
                                                           value="<?php echo (isset($marketplaceAccount->config['percentSalePrice'])) ? $marketplaceAccount->config['percentSalePrice'] : ''; ?>"
                                                           required="required"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php
                                                if (isset($marketplaceAccount->config['optradioSalePrice'])) {
                                                    switch ($marketplaceAccount->config['optradioSalePrice']) {
                                                        case "-0.5":
                                                            echo '<label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                         name="optradioSalePrice" checked value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                            break;
                                                        case "+0.5":
                                                            echo '<label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                         name="optradioSalePrice" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" checked value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                            break;
                                                        case "+1":
                                                            echo '<label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                         name="optradioSalePrice" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice"  value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" checked value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                            break;
                                                        case "-1":
                                                            echo '<label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                         name="optradioSalePrice" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice"  value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" checked value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                            break;
                                                    }
                                                } else {
                                                    echo '<label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                         name="optradioSalePrice" value="-0.5">Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="+1" checked>Arrotonda
                                                    eccesso a Euro 1,0</label>';
                                                } ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dateStartPeriod1">1° indica data inizio</label>
                                                <input type="date" class="form-control" id="dateStartPeriod1"
                                                       name="dateStartPeriod1"
                                                       value="<?php echo (isset($marketplaceAccount->config['dateStartPeriod1'])) ? $marketplaceAccount->config['dateStartPeriod1'] : ''; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dateEndPeriod1">1° indica data Fine</label>
                                                <input type="date" class="form-control" id="dateEndPeriod1"
                                                       name="dateEndPeriod1"
                                                       value="<?php echo (isset($marketplaceAccount->config['dateEndPeriod1'])) ? $marketplaceAccount->config['dateEndPeriod1'] : ''; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dateStartPeriod2">2° indica data inizio</label>
                                                <input type="date" class="form-control" id="dateStartPeriod2"
                                                       name="dateStartPeriod2"
                                                       value="<?php echo (isset($marketplaceAccount->config['dateStartPeriod2'])) ? $marketplaceAccount->config['dateStartPeriod2'] : ''; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dateEndPeriod2">2° indica data Fine</label>
                                                <input type="date" class="form-control" id="dateEndPeriod2"
                                                       name="dateEndPeriod2"
                                                       value="<?php echo (isset($marketplaceAccount->config['dateEndPeriod2'])) ? $marketplaceAccount->config['dateEndPeriod2'] : ''; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dateStartPeriod3">3° indica data inizio</label>
                                                <input type="date" class="form-control" id="dateStartPeriod3"
                                                       name="dateStartPeriod3"
                                                       value="<?php echo (isset($marketplaceAccount->config['dateStartPeriod3'])) ? $marketplaceAccount->config['dateStartPeriod3'] : ''; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dateEndPeriod3">3° indica data Fine</label>
                                                <input type="date" class="form-control" id="dateEndPeriod3"
                                                       name="dateEndPeriod3"
                                                       value="<?php echo (isset($marketplaceAccount->config['dateEndPeriod3'])) ? $marketplaceAccount->config['dateEndPeriod3'] : ''; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dateStartPeriod4">4° indica data inizio</label>
                                                <input type="date" class="form-control" id="dateStartPeriod4"
                                                       name="dateStartPeriod4"
                                                       value="<?php echo (isset($marketplaceAccount->config['dateStartPeriod4'])) ? $marketplaceAccount->config['dateStartPeriod4'] : ''; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dateEndPeriod4">4° indica data Fine</label>
                                                <input type="date" class="form-control" id="dateEndPeriod4"
                                                       name="dateEndPeriod4"
                                                       value="<?php echo (isset($marketplaceAccount->config['dateEndPeriod4'])) ? $marketplaceAccount->config['dateEndPeriod4'] : ''; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="brandExclusion">Seleziona i Brand da escludere
                                                </label>
                                                <select id="brandExclusion"
                                                        name="brandExclusion"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione dei Brand da Escludere"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="text" id="brandSaleExclusion" name="brandSaleExclusion"
                                                   value="<?php echo (isset($marketplaceAccount->config['brandSaleExclusion'])) ? $marketplaceAccount->config['brandSaleExclusion'] : ''; ?>"/>
                                        </div>
                                    </div>
                                    <?php  $brandSaleExists = explode(',',$marketplaceAccount->config['brandSaleExclusion']);
                                    foreach($brandSaleExists as $brandSaleExist ){
                                        $productBrands=\Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id'=>$brandSaleExist]);
                                        if($productBrands){
                                            echo '<div class="row" id="brandSaleDiv-'.$productBrands->id.'"><div class="col-md-2">'.$productBrands->name.'</div><div class="col-md-2"> <button class="success" id="btn-'.$productBrands->id.'" onclick="lessBrandSale('.$productBrands->id.')" type="button"><span  class="fa fa-close"></span></button></div></div>';
                                        }
                                    }
                                    ?>
                                    <div id="appendBrandsSalePublishPar">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="ruleNameProduct" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Regole sul Nome Prodotto </h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                            if (isset($marketplaceAccount->config['checkNameCatalog'])) {
                                                if ($marketplaceAccount->config['checkNameCatalog'] == "1") {
                                                    echo ' <label class="radio-inline"><input type="radio" id="checkNameCatalog"
                                                                               name="checkNameCatalog" checked value="1"
                                                >Nome Attivo da Catalogo</label>
                                            <label class="radio-inline"><input type="radio" id="checkNameCatalog"
                                                                               name="checkNameCatalog" value="2">Nome
                                                Attivo Personalizzato</label>';
                                                } else {
                                                    echo ' <label class="radio-inline"><input type="radio" id="checkNameCatalog"
                                                                               name="checkNameCatalog" value="1"
                                                >Nome Attivo da Catalogo</label>
                                            <label class="radio-inline"><input type="radio" id="checkNameCatalog"
                                                                               name="checkNameCatalog" checked value="2">Nome
                                                Attivo Personalizzato</label>';
                                                }
                                            } else {
                                                echo ' <label class="radio-inline"><input type="radio" id="checkNameCatalog"
                                                                               name="checkNameCatalog" checked value="1"
                                                >Nome Attivo da Catalogo</label>
                                            <label class="radio-inline"><input type="radio" id="checkNameCatalog"
                                                                               name="checkNameCatalog" value="2">Nome
                                                Attivo Personalizzato</label>';
                                            }
                                            ?>

                                        </div>
                                    </div>
                                    <div id="rawName">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php
                                                if (isset($marketplaceAccount->config['optradioName'])) {
                                                    if ($marketplaceAccount->config['optradioName'] == "1") {
                                                        echo '<label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="1"
                                                                                   checked>Applicare Composizione del
                                                    nome se scostamento applicato su prezzo attivo brand - Sconto del %
                                                    da prezzo Attivo a prezzo ricalcolato –cpf -colore</label>
                                                <label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="0">Non
                                                    Applicare Composizione del nome se scostamento applicato su prezzo
                                                    attivo brand - Sconto del % da prezzo Pieno a prezzo ricalcolato
                                                    –cpf -colore</label>';
                                                    } else {
                                                        echo '<label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="1"
                                                                                   >Applicare Composizione del
                                                    nome se scostamento applicato su prezzo attivo brand - Sconto del %
                                                    da prezzo Attivo a prezzo ricalcolato –cpf -colore</label>
                                                <label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" checked value="0">Non
                                                    Applicare Composizione del nome se scostamento applicato su prezzo
                                                    attivo brand - Sconto del % da prezzo Pieno a prezzo ricalcolato
                                                    –cpf -colore</label>';
                                                    }
                                                } else {
                                                    echo '<label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="1"
                                                                                   checked>Applicare Composizione del
                                                    nome se scostamento applicato su prezzo attivo brand - Sconto del %
                                                    da prezzo Attivo a prezzo ricalcolato –cpf -colore</label>
                                                <label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="0">Non
                                                    Applicare Composizione del nome se scostamento applicato su prezzo
                                                    attivo brand - Sconto del % da prezzo Pieno a prezzo ricalcolato
                                                    –cpf -colore</label>';

                                                } ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="ruleAttribution" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Regole di Attribuzione </h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                            if (isset($marketplaceAccount->config['typeAssign'])) {
                                                if ($marketplaceAccount->config['typeAssign'] == "1") {
                                                    echo '<label class="radio-inline"><input type="radio" id="typeAssign"
                                                                               name="typeAssign" checked value="1">Assegnazione
                                                Automatica tutti i Brand</label>
                                            <label class="radio-inline"><input type="radio" id="typeAssign"
                                                                               name="typeAssign" value="2">Assegnazione
                                                Per Brand con opzioni di esclusione</label>';
                                                } else {
                                                    echo '<label class="radio-inline"><input type="radio" id="typeAssign"
                                                                               name="typeAssign" value="1">Assegnazione
                                                Automatica tutti i Brand</label>
                                            <label class="radio-inline"><input type="radio" id="typeAssign"
                                                                               name="typeAssign" checked value="2">Assegnazione
                                                Per Brand con opzioni di esclusione</label>';
                                                }
                                            } else {
                                                echo '<label class="radio-inline"><input type="radio" id="typeAssign"
                                                                               name="typeAssign"  checked value="1">Assegnazione
                                                Automatica tutti i Brand</label>
                                            <label class="radio-inline"><input type="radio" id="typeAssign"
                                                                               name="typeAssign" value="2">Assegnazione
                                                Per Brand con opzioni di esclusione</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div id="rawRule" class="hide">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="BrandId">Seleziona i Brand da escludere
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
                                                <input type="text" id="brandis" name="brandis"
                                                       value="<?php echo (isset($marketplaceAccount->config['brands'])) ? $marketplaceAccount->config['brands'] : ''; ?>"/>
                                            </div>
                                        </div>
                                        <?php  $brandExists = explode(',',$marketplaceAccount->config['brands']);
                                        foreach($brandExists as $brandExist ){
                                            $productBrands=\Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id'=>$brandExist]);
                                            if($productBrands){
                                                echo '<div class="row" id="brandDiv-'.$productBrands->id.'"><div class="col-md-2">'.$productBrands->name.'</div><div class="col-md-2"> <button class="success" id="btn-'.$productBrands->id.'" onclick="lessBrand('.$productBrands->id.')" type="button"><span  class="fa fa-close"></span></button></div></div>';
                                            }
                                        }
                                        ?>
                                        <div id="appendBrandsPublishPar">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="ruleAttributionParallel" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Regole di Attribuzione Prodotti Paralleli</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                            if (isset($marketplaceAccount->config['typeAssignParallel'])) {
                                                if ($marketplaceAccount->config['typeAssignParallel'] == "1") {
                                                    echo '<label class="radio-inline"><input type="radio" checked id="typeAssignParallel"
                                                                               name="typeAssignParallel" value="1">Assegnazione
                                                Automatica tutti i Brand</label>
                                            <label class="radio-inline"><input type="radio" id="typeAssignParallel"
                                                                               name="typeAssignParallel" value="2">Assegnazione
                                                Per Brand con opzioni di esclusione</label>';
                                                } else {
                                                    echo '<label class="radio-inline"><input type="radio" id="typeAssignParallel"
                                                                               name="typeAssignParallel" value="1">Assegnazione
                                                Automatica tutti i Brand</label>
                                            <label class="radio-inline"><input type="radio" id="typeAssignParallel"
                                                                               name="typeAssignParallel" checked value="2">Assegnazione
                                                Per Brand con opzioni di esclusione</label>';
                                                }
                                            } else {
                                                echo '<label class="radio-inline"><input type="radio" id="typeAssignParallel"
                                                                               name="typeAssignParallel" checked value="1">Assegnazione
                                                Automatica tutti i Brand</label>
                                            <label class="radio-inline"><input type="radio" id="typeAssignParallel"
                                                                               name="typeAssignParallel" value="2">Assegnazione
                                                Per Brand con opzioni di esclusione</label>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div id="rawRuleParallel" class="hide">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="BrandIdParallel">Seleziona i Brand da escludere
                                                    </label>
                                                    <select id="BrandIdParallel"
                                                            name="BrandIdParallel"
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
                                                           value="<?php echo (isset($marketplaceAccount->config['brandParallel'])) ? $marketplaceAccount->config['brandParallel'] : ''; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                        <?php  $brandExistsParallel=explode(',',$marketplaceAccount->config['brandParallel']);
                                        foreach($brandExistsParallel as $brandExistParallel ){
                                            $productBrands=\Monkey::app()->repoFactory->create('ProductBrand')->findOneBy(['id'=>$brandExistParallel]);
                                            if($productBrands){
                                                echo '<div class="row" id="brandParallelDiv-'.$productBrands->id.'"><div class="col-md-2">'.$productBrands->name.'</div><div class="col-md-2"> <button class="success" id="btnParallel-'.$productBrands->id.'" onclick="lessBrandParallel('.$productBrands->id.')" type="button"><span  class="fa fa-close"></span></button></div></div>';
                                            }
                                        }
                                        ?>
                                        <div id="appendBrandsPar">
                                        </div>
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


<?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni Marketplace">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.marketplacehasshop-account.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>