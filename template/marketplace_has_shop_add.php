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
                            <button class="tablinks" onclick="openTab(event, 'ruleNameProduct')">Modifica Nome Prodotto</button>
                            <button class="tablinks" onclick="openTab(event, 'ruleNameProduct')">Modifica Nome Prodotto</button>
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
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="marketplace_account_name">Nome</label>
                                                <input id="marketplace_account_name" autocomplete="off" type="text"
                                                       class="form-control" name="marketplace_account_name" value=""
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
                                                       class="form-control" name="slug" value=""
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-primary" name="uploadLogo"
                                                    id="uploadLogo">carica Logo
                                            </button>
                                            <input id="logoFile" type="hidden" value=""/>
                                            <div id="returnFileLogo"></div>
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
                                                    <option value="1">Si</option>
                                                    <option value="0">No</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default required">
                                                <label for="nameAdminister">Account Manager</label>
                                                <input id="nameAdminister" autocomplete="off" type="text"
                                                       class="form-control" name="nameAdminister" value=""
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default required">
                                                <label for="emailNotify">Email Notifica </label>
                                                <input id="emailNotify" autocomplete="off" type="text"
                                                       class="form-control" name="emailNotify" value=""
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="shopId">Seleziona Lo Shop
                                                    </label>
                                                    <select id="shopId"
                                                            name="shopId"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione lo Shop"
                                                            data-init-plugin="selectize">
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
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                ​<input type="checkbox" class="form-control" id="activePriceCatalogue"
                                                        name="activePriceCatalogue"
                                                        required="required"
                                                />
                                                <p class="text-center">Prezzo Attivo no Saldi da Catalogo</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                ​<input type="checkbox" class="form-control" id="activePriceSpecial"
                                                        name="activePriceSpecial"
                                                        required="required"
                                                />
                                                <p class="text-center">Prezzo Attivo no saldi Personalizzato</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="rawPrice">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="signFullPrice">Indica scostamento rispetto al listino +/-
                                                    </label>
                                                    <select id="signFullPrice"
                                                            name="signFullPrice"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione il Segno"
                                                            data-init-plugin="selectize">
                                                        <option value="">seleziona</option>
                                                        <option value="+">Maggiorazione +</option>
                                                        <option value="-">Diminuzione - </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="percentFullPrice">Percentuale %</label>
                                                    <input id="percentFullPrice" autocomplete="off" type="text"
                                                           class="form-control" name="percentFullPrice" value=""
                                                           required="required"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-0.5" checked>Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradio"
                                                                                   name="optradio" value="+1">Arrotonda
                                                    eccesso a Euro 1,0</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="radio-inline"><input type="radio" id="optradioactive"
                                                                                   name="optradioactive"
                                                                                   value="activePrice" checked>Scostamento
                                                    applicato su prezzo attivo
                                                </label>
                                                <label class="radio-inline"><input type="radio" id="optradioactive"
                                                                                   name="optradioactive"
                                                                                   value="fullPrice">Scostamento
                                                    applicato su prezzo pieno
                                                </label>
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
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                ​<input type="checkbox" class="form-control" id="activeSalePriceCatalogue"
                                                        name="activeSalePriceCatalogue"
                                                        required="required"
                                                />
                                                <p class="text-center">Saldi Attivi come da Catalogo</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                ​<input type="checkbox" class="form-control" id="activeSalePriceSpecial"
                                                        name="activeSalePriceSpecial"
                                                        required="required"
                                                />
                                                <p class="text-center">Saldi Attivi personalizzati</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="rawPrice">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="signSalePrice">Indica scostamento rispetto al listino in saldo catalogo +/-
                                                    </label>
                                                    <select id="signSalePrice"
                                                            name="signSalePrice"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione Segno"
                                                            data-init-plugin="selectize">
                                                        <option value="">seleziona</option>
                                                        <option value="+">Maggiorazione +</option>
                                                        <option value="-">Diminuzione -</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="percentSalePrice">Percentuale %</label>
                                                    <input id="percentSalePride" autocomplete="off" type="text"
                                                           class="form-control" name="percentSalePrice" value=""
                                                           required="required"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="-0.5" checked>Arrotonda
                                                    difetto a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="+0.5">Arrotonda
                                                    eccesso a Euro 0,5</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="-1">Arrotonda
                                                    difetto a Euro 1,0</label>
                                                <label class="radio-inline"><input type="radio" id="optradioSalePrice"
                                                                                   name="optradioSalePrice" value="+1">Arrotonda
                                                    eccesso a Euro 1,0</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="dateStartPeriod1">1° indica data inizio</label>
                                                    <input type="date" class="form-control" id="dateStartPeriod1" name="dateStartPeriod1" value="" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="dateEndPeriod1">1° indica data Fine</label>
                                                    <input type="date" class="form-control" id="dateEndPeriod1" name="dateEndPeriod1" value="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="dateStartPeriod2">2° indica data inizio</label>
                                                    <input type="date" class="form-control" id="dateStartPeriod2" name="dateStartPeriod2" value="" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="dateEndPeriod2">2° indica data Fine</label>
                                                    <input type="date" class="form-control" id="dateEndPeriod2" name="dateEndPeriod2" value="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="dateStartPeriod3">3° indica data inizio</label>
                                                    <input type="date" class="form-control" id="dateStartPeriod3" name="dateStartPeriod3" value="" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="dateEndPeriod3">3° indica data Fine</label>
                                                    <input type="date" class="form-control" id="dateEndPeriod3" name="dateEndPeriod3" value="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="dateStartPeriod4">4° indica data inizio</label>
                                                    <input type="date" class="form-control" id="dateStartPeriod4" name="dateStartPeriod4" value="" />
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="dateEndPeriod4">4° indica data Fine</label>
                                                    <input type="date" class="form-control" id="dateEndPeriod4" name="dateEndPeriod4" value="" />
                                                </div>
                                            </div>
                                        </div>
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
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                ​<input type="checkbox" class="form-control" id="checkNameCatalog"
                                                        name="checkNameCatalog"
                                                        required="required"
                                                />
                                                <p class="text-center">Nome Attivo da Catalogo</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                ​<input type="checkbox" class="form-control" id="checkCustomNameCatalog"
                                                        name="checkCustomNameCatalog"
                                                        required="required"
                                                />
                                                <p class="text-center">Nome prodotto attivo personalizzato</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="rawPrice">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="signSalePrice">Indica scostamento rispetto al listino in saldo catalogo +/-
                                                    </label>
                                                    <select id="signSalePrice"
                                                            name="signSalePrice"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione Segno"
                                                            data-init-plugin="selectize">
                                                        <option value="">seleziona</option>
                                                        <option value="+">Maggiorazione +</option>
                                                        <option value="-">Diminuzione -</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="percentSalePrice">Percentuale %</label>
                                                    <input id="percentSalePride" autocomplete="off" type="text"
                                                           class="form-control" name="percentSalePrice" value=""
                                                           required="required"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="1" checked>Applicare Composizione del nome se scostamento applicato su prezzo attivo  brand - Sconto del % da prezzo Attivo  a prezzo ricalcolato –cpf -colore</label>
                                                <label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="0">Non Applicare Composizione del nome se scostamento applicato su prezzo attivo brand - Sconto del % da prezzo Pieno  a prezzo ricalcolato –cpf -colore</label>
                                            </div>
                                        </div>
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
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                ​<input type="checkbox" class="form-control" id="checkNameCatalog"
                                                        name="checkNameCatalog"
                                                        required="required"
                                                />
                                                <p class="text-center">Nome Attivo da Catalogo</p>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default">
                                                ​<input type="checkbox" class="form-control" id="checkCustomNameCatalog"
                                                        name="checkCustomNameCatalog"
                                                        required="required"
                                                />
                                                <p class="text-center">Nome prodotto attivo personalizzato</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="rawPrice">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="signSalePrice">Indica scostamento rispetto al listino in saldo catalogo +/-
                                                    </label>
                                                    <select id="signSalePrice"
                                                            name="signSalePrice"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione Segno"
                                                            data-init-plugin="selectize">
                                                        <option value="">seleziona</option>
                                                        <option value="+">Maggiorazione +</option>
                                                        <option value="-">Diminuzione -</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="percentSalePrice">Percentuale %</label>
                                                    <input id="percentSalePride" autocomplete="off" type="text"
                                                           class="form-control" name="percentSalePrice" value=""
                                                           required="required"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="1" checked>Applicare Composizione del nome se scostamento applicato su prezzo attivo  brand - Sconto del % da prezzo Attivo  a prezzo ricalcolato –cpf -colore</label>
                                                <label class="radio-inline"><input type="radio" id="optradioName"
                                                                                   name="optradioName" value="0">Non Applicare Composizione del nome se scostamento applicato su prezzo attivo brand - Sconto del % da prezzo Pieno  a prezzo ricalcolato –cpf -colore</label>
                                            </div>
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


    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni Aggregatore">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.marketplace-account.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>