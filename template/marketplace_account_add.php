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
                            <button class="tablinks" onclick="openTab(event, 'insertAggregator')">Parametri Pubblicazione
                                aggregatore
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'insertDepublishAggregator')">Parametri Depubblicazione
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'insertRules')">Brand</button>
                            <button class="tablinks" onclick="openTab(event, 'insertCampaign')">Campagna</button>
                        </div>
                    </div>
                </div>
                <div id="insertAggregator" class="tabcontent">
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
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <label for="nameAdminister">Account Manager</label>
                                                <input id="nameAdminister" autocomplete="off" type="text"
                                                       class="form-control" name="nameAdminister" value=""
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <label for="emailNotify">Email Notifica </label>
                                                <input id="emailNotify" autocomplete="off" type="text"
                                                       class="form-control" name="emailNotify" value=""
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="useRange">Utilizzare le fascie per Cpc
                                                </label>
                                                <select id="useRange"
                                                        name="useRange"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona se utilizzare le fascie per il Calcolo del Cpc"
                                                        data-init-plugin="selectize">
                                                    <option value=""></option>
                                                    <option value="1">Si</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="aggregatorHasShopId">Seleziona Account Aggregatore
                                                    </label>
                                                    <select id="aggregatorHasShopId"
                                                            name="aggregatorHasShopId"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione Account Aggregatore"
                                                            data-init-plugin="selectize">
                                                    </select>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="timeRange">Calcolo giorni</label>
                                                <input id="timeRange" autocomplete="off" type="text"
                                                       class="form-control" name="timeRange" value="7"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="multiplierDefault"> Moltiplicatore di Default </label>
                                                <input id="multiplierDefault" autocomplete="off" type="text"
                                                       class="form-control" name="multiplierDefault" value="0.1"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="defaultCpcF">Cpc Fornitore Desktop</label>
                                                <input id="defaultCpcF" autocomplete="off" type="text"
                                                       class="form-control" name="defaultCpcF" value="0.19"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="defaultCpcFM">Cpc Fornitore Mobile </label>
                                                <input id="defaultCpcFM" autocomplete="off" type="text"
                                                       class="form-control" name="defaultCpcFM" value="0.19"
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="defaultCpc">Default Cpc Desktop</label>
                                                <input id="defaultCpc" autocomplete="off" type="text"
                                                       class="form-control" name="defaultCpc" value="0.19"
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="defaultCpcM">Default Cpc Mobile </label>
                                                <input id="defaultCpcM" autocomplete="off" type="text"
                                                       class="form-control" name="defaultCpcM" value="0.19"
                                                       required="required"/>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="row" id="divmarketplace"></div>
                                    <div class="row">
                                        <div class="col-md-12">Fascia 1
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="priceModifierRange1">Range 1 Retail Price </label>
                                                <input id="priceModifierRange1" autocomplete="off" type="text"
                                                       class="form-control" name="priceModifierRange1" value="0-200"
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="range1Cpc">Cpc 1 Dedicato Desktop</label>
                                                <input id="range1Cpc" autocomplete="off" type="text"
                                                       class="form-control" name="range1Cpc" value="0.19"
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="range1CpcM">Cpc 1 Dedicato Mobile</label>
                                                <input id="range1CpcM" autocomplete="off" type="text"
                                                       class="form-control" name="range1CpcM" value="0.19"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productSizeGroupId1">Selettore
                                                        Gruppo Taglia per il range 1
                                                    </label>
                                                    <select id="productSizeGroupId1"
                                                            name="productSizeGroupId1"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione il Gruppo Taglia 1"
                                                            data-init-plugin="selectize">

                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productCategoryId1">Selettore
                                                        Categoria per il range
                                                        1</label>
                                                    <select id="productCategoryId1"
                                                            name="productCategoryId1"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione la Categoria 1"
                                                            data-init-plugin="selectize">
                                                        <?php echo $productCategory1Option ?>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">Fascia 2
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="priceModifierRange2">Range 2 Retail Price </label>
                                                    <input id="priceModifierRange2" autocomplete="off" type="text"
                                                           class="form-control" name="priceModifierRange2" value="201-500"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="range2Cpc">Cpc 2 Dedicato Desktop</label>
                                                    <input id="range2Cpc" autocomplete="off" type="text"
                                                           class="form-control" name="range2Cpc" value="0.19"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="range2CpcM">Cpc 2 Dedicato Mobile</label>
                                                    <input id="range2CpcM" autocomplete="off" type="text"
                                                           class="form-control" name="range2CpcM" value="0.19"
                                                           required="required"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productSizeGroupId2">Selettore
                                                            Gruppo Taglia per il range
                                                            2</label>
                                                        <select id="productSizeGroupId2"
                                                                name="productSizeGroupId2"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione il Gruppo Taglia"
                                                                data-init-plugin="selectize">

                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productCategoryId2">Selettore
                                                            Categoria per il range
                                                            2</label>
                                                        <select id="productCategoryId2"
                                                                name="productCategoryId2"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione la Categoria 2"
                                                                data-init-plugin="selectize">
                                                            <?php echo $productCategory2Option ?>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">Fascia 3
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="priceModifierRange3">Range 3 Retail Price </label>
                                                <input id="priceModifierRange3" autocomplete="off" type="text"
                                                       class="form-control" name="priceModifierRange3" value="501-1000"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="range3Cpc">Cpc 3 Dedicato Desktop</label>
                                                <input id="range3Cpc" autocomplete="off" type="text"
                                                       class="form-control" name="range3Cpc" value="0.19"
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="range3CpcM">Cpc 3 Dedicato Mobile</label>
                                                <input id="range3CpcM" autocomplete="off" type="text"
                                                       class="form-control" name="range3CpcM" value="0.19"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productSizeGroupId3">Selettore
                                                        Gruppo Taglia per il range
                                                        3</label>
                                                    <select id="productSizeGroupId3"
                                                            name="productSizeGroupId3"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione il Gruppo Taglia"
                                                            data-init-plugin="selectize">
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productCategoryId3">Selettore
                                                        Categoria per il range
                                                        3</label>
                                                    <select id="productCategoryId3"
                                                            name="productCategoryId3"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione la Categoria 3"
                                                            data-init-plugin="selectize">
                                                        <?php echo $productCategory3Option ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">Fascia 4
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="priceModifierRange4">Range 4 Retail Price </label>
                                                <input id="priceModifierRange4" autocomplete="off" type="text"
                                                       class="form-control" name="priceModifierRange4" value="1001-5000"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="range4Cpc">Cpc 4 Dedicato Desktop</label>
                                                <input id="range4Cpc" autocomplete="off" type="text"
                                                       class="form-control" name="range4Cpc" value="0.19"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="range4CpcM">Cpc 4 Dedicato Mobile</label>
                                                <input id="range4CpcM" autocomplete="off" type="text"
                                                       class="form-control" name="range4CpcM" value="0.19"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productSizeGroupId4">Selettore
                                                        Gruppo Taglia per il range
                                                        4</label>
                                                    <select id="productSizeGroupId4"
                                                            name="productSizeGroupId4"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione il Gruppo Taglia"
                                                            data-init-plugin="selectize">

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productCategoryId4">Selettore
                                                        Categoria per il range
                                                        4</label>
                                                    <select id="productCategoryId4"
                                                            name="productCategoryId4"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione la Categoria 4"
                                                            data-init-plugin="selectize">
                                                        <?php echo $productCategory4Option ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">Fascia 5
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="priceModifierRange5">Range 5 Retail Price </label>
                                                <input id="priceModifierRange5" autocomplete="off" type="text"
                                                       class="form-control" name="priceModifierRange5" value="10001-1000000"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="range5Cpc">Cpc 5 Dedicato Desktop</label>
                                                <input id="range5Cpc" autocomplete="off" type="text"
                                                       class="form-control" name="range5Cpc" value="0.19"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="range5CpcM">Cpc 5 Dedicato Mobile</label>
                                                <input id="range5CpcM" autocomplete="off" type="text"
                                                       class="form-control" name="range5CpcM" value="0.19"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productSizeGroupId5">Selettore
                                                        Gruppo Taglia per il range
                                                        5</label>
                                                    <select id="productSizeGroupId5"
                                                            name="productSizeGroupId5"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione il Gruppo Taglia"
                                                            data-init-plugin="selectize">

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="productCategoryId5">Selettore
                                                        Categoria per il range
                                                        5</label>
                                                    <select id="productCategoryId5"
                                                            name="productCategoryId5"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione la Categoria 4"
                                                            data-init-plugin="selectize">
                                                        <?php echo $productCategory5Option ?>
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
                <div id="insertDepublishAggregator" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">informazioni di base</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isActiveDepublisher">Seleziona se Attivo il Job di depubblicazione(Eliseo)
                                                </label>
                                                <select id="isActiveDepublisher"
                                                        name="isActiveDepublisher"
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
                                    <div id="rawDepublish">
                                        <div class="row">
                                            <div class="col-md-1">Budget Mensile
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget01">Gennaio</label>
                                                    <input id="budget01" autocomplete="off" type="text"
                                                           class="form-control" name="budget01" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget02">Febbraio</label>
                                                    <input id="budget02" autocomplete="off" type="text"
                                                           class="form-control" name="budget02" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget03">Marzo</label>
                                                    <input id="budget03" autocomplete="off" type="text"
                                                           class="form-control" name="budget03" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget04">Aprile</label>
                                                    <input id="budget04" autocomplete="off" type="text"
                                                           class="form-control" name="budget04" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget05">Maggio</label>
                                                    <input id="budget05" autocomplete="off" type="text"
                                                           class="form-control" name="budget05" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget06">Giugno</label>
                                                    <input id="budget06" autocomplete="off" type="text"
                                                           class="form-control" name="budget06" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget07">Luglio</label>
                                                    <input id="budget07" autocomplete="off" type="text"
                                                           class="form-control" name="budget07" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget08">Agosto</label>
                                                    <input id="budget08" autocomplete="off" type="text"
                                                           class="form-control" name="budget08" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget09">Settembre</label>
                                                    <input id="budget09" autocomplete="off" type="text"
                                                           class="form-control" name="budget09" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget10">Ottobre</label>
                                                    <input id="budget10" autocomplete="off" type="text"
                                                           class="form-control" name="budget10" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget11">Novembre</label>
                                                    <input id="budget11" autocomplete="off" type="text"
                                                           class="form-control" name="budget11" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <label for="budget12">Dicembre</label>
                                                    <input id="budget12" autocomplete="off" type="text"
                                                           class="form-control" name="budget12" value="100"
                                                           required="required"/>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" id="source_label_productCategoryGroup">
                                            <div class="col-md-12">Esclusione Categorie
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productCategoryIdEx1">Selettore
                                                            Categoria 1 da Escludere
                                                        </label>
                                                        <select id="productCategoryIdEx1"
                                                                name="productCategoryIdEx1"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione la Categoria 1 da Escludere "
                                                                data-init-plugin="selectize">
                                                            <?php echo $productCategoryEx1Option ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productCategoryIdEx2">Selettore
                                                            Categoria 2 da Escludere
                                                        </label>
                                                        <select id="productCategoryIdEx2"
                                                                name="productCategoryIdEx2"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione la Categoria 2 da Escludere "
                                                                data-init-plugin="selectize">
                                                            <?php echo $productCategoryEx2Option ?>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productCategoryIdEx3">Selettore
                                                            Categoria 3 da Escludere
                                                        </label>
                                                        <select id="productCategoryIdEx3"
                                                                name="productCategoryIdEx3"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione la Categoria 3 da Escludere "
                                                                data-init-plugin="selectize">
                                                            <?php echo $productCategoryEx3Option ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productCategoryIdEx4">Selettore
                                                            Categoria 4 da Escludere
                                                        </label>
                                                        <select id="productCategoryIdEx4"
                                                                name="productCategoryIdEx4"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione la Categoria 4 da Escludere "
                                                                data-init-plugin="selectize">
                                                            <?php echo $productCategoryEx4Option ?>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productCategoryIdEx5">Selettore
                                                            Categoria 5 da Escludere
                                                        </label>
                                                        <select id="productCategoryIdEx5"
                                                                name="productCategoryIdEx5"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione la Categoria 5 da Escludere "
                                                                data-init-plugin="selectize">
                                                            <?php echo $productCategoryEx5Option ?>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group form-group-default required">
                                                        <div class="form-group form-group-default selectize-enabled">
                                                            <label for="productCategoryIdEx6">Selettore
                                                                Categoria 6 da Escludere
                                                            </label>
                                                            <select id="productCategoryIdEx6"
                                                                    name="productCategoryIdEx6"
                                                                    class="full-width selectpicker"
                                                                    placeholder="Selezione la Categoria 6 da Escludere "
                                                                    data-init-plugin="selectize">
                                                                <?php echo $productCategoryEx6Option ?>
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--gruppo taglie inizio -->
                                        <div class="row" id="source_label_productSizeGroup">
                                            <div class="col-md-12">Esclusione Gruppi Taglia
                                            </div>
                                        </div>
                                        <div class="row" id="source_group_productSizeGroup">
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productSizeGroupEx1">Selettore
                                                            Gruppo Taglia 1 da Escludere
                                                        </label>
                                                        <select id="productSizeGroupEx1"
                                                                name="productSizeGroupEx1"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione il Gruppo Taglia da Escludere"
                                                                data-init-plugin="selectize">

                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productSizeGroupEx2">Selettore Gruppo
                                                            Taglia
                                                            2 da Escludere</label>
                                                        <select id="productSizeGroupEx2"
                                                                name="productSizeGroupEx2"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione il Gruppo Taglia"
                                                                data-init-plugin="selectize">

                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productSizeGroupEx3">Selettore Gruppo
                                                            Taglia
                                                            3 da Escludere</label>
                                                        <select id="productSizeGroupEx3"
                                                                name="productSizeGroupEx3"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione il Gruppo Taglia"
                                                                data-init-plugin="selectize">

                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productSizeGroupEx4">Selettore Gruppo
                                                            Taglia
                                                            4 da Escludere</label>
                                                        <select id="productSizeGroupEx4"
                                                                name="productSizeGroupEx4"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione il Gruppo Taglia"
                                                                data-init-plugin="selectize">

                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productSizeGroupEx5">Selettore Gruppo
                                                            Taglia
                                                            5 da Escludere</label>
                                                        <select id="productSizeGroupEx5"
                                                                name="productSizeGroupEx5"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione il Gruppo Taglia"
                                                                data-init-plugin="selectize">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group form-group-default required">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <label for="productSizeGroupEx6">Selettore Gruppo
                                                            Taglia
                                                            6 da Escludere</label>
                                                        <select id="productSizeGroupEx6"
                                                                name="productSizeGroupEx6"
                                                                class="full-width selectpicker"
                                                                placeholder="Selezione il Gruppo Taglia"
                                                                data-init-plugin="selectize">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="insertRules" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Inserimento Regole</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="nameRule">Nome Regola </label>
                                                <input id="nameRule" autocomplete="off" type="text"
                                                       class="form-control" name="nameRule" value=""
                                                       required="required"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="rawBrands">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="insertCampaign" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Campagna</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="selectCreationCampaign">Devi Creare la Campagna ?
                                                    </label>
                                                    <select id="selectCreationCampaign"
                                                            name="selectCreationCampaign"
                                                            class="full-width selectpicker"
                                                            placeholder="Selezione se devi creare la campagna "
                                                            data-init-plugin="selectize">
                                                        <option value=""></option>
                                                        <option value="1">Si</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="divcampaign">

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