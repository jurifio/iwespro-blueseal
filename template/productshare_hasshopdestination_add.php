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
                            <button class="tablinks" onclick="openTab(event, 'insertShareShop')">Crea Regole dei
                                Prodotti
                                Paralleli
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'ruleSharing')">Regole di
                                Condivisione
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'rulePublish')">Regole di
                                Pubblicazione
                            </button>
                        </div>
                    </div>
                </div>
                <div id="insertShareShop" class="tabcontent">
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
                                            <div class="form-group form-group-default selectize-enabled required">
                                                <label for="shopId">Seleziona Lo Shop
                                                </label>
                                                <select id="shopId"
                                                        name="shopId"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione lo Shop"
                                                        data-init-plugin="selectize"
                                                        required="required">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="marketplaceId">Seleziona il Sito dello Shop
                                                </label>
                                                <select id="marketplaceId"
                                                        name="marketplaceId"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione il Sito"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>

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

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="ruleSharing" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Regole di Condivisione Prodotti </h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isActiveShare">Seleziona se Attiva la Condivisione
                                                </label>
                                                <select id="isActiveShare"
                                                        name="isActiveShare"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione se attiva La Condivisione"
                                                        data-init-plugin="selectize">
                                                    <option value=""></option>
                                                    <option value="1">Si</option>
                                                    <option value="0">No</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <label class="radio-inline"><input type="radio" id="typeAssign"
                                                                               name="typeAssign" value="1">Assegnazione
                                                Automatica tutti i Brand</label>
                                            <label class="radio-inline"><input type="radio" id="typeAssign"
                                                                               name="typeAssign" value="2">Assegnazione
                                                Per Brand con opzioni di esclusione</label>
                                        </div>
                                    </div>
                                    <div id="rawRule" class="hide">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="BrandId">Seleziona i Brand da escludere (Devi Selezionare prima lo Shop per popolare la selezione)
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
                                                <input type="text" id="brands" name="brands" value="0"/>
                                            </div>
                                        </div>
                                        <div id="appendBrandsPublishPar">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="rulePublish" class="tabcontent">
                    <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                          autocomplete="off">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default clearfix">
                                    <div class="panel-heading clearfix">
                                        <h5 class="m-t-12">Regole di Pubblicazione Prodotti Paralleli</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isActivePublish">Seleziona se Attiva la Pubblicazione (Devi Selezionare prima lo Shop per popolare la selezione)
                                                </label>
                                                <select id="isActivePublish"
                                                        name="isActivePublish"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione se attiva La Pubblicazione"
                                                        data-init-plugin="selectize">
                                                    <option value=""></option>
                                                    <option value="1">Si</option>
                                                    <option value="0">No</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="radio-inline"><input type="radio" id="typeAssignParallel"
                                                                               name="typeAssignParallel" value="1">Assegnazione
                                                Automatica tutti i Brand</label>
                                            <label class="radio-inline"><input type="radio" id="typeAssignParallel"
                                                                               name="typeAssignParallel" value="2">Assegnazione
                                                Per Brand con opzioni di esclusione</label>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="productStatusId">Seleziona lo Stato Con Cui verranno
                                                    pubblicati i Prodotti
                                                </label>
                                                <select id="productStatusId"
                                                        name="productStatusId"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione Lo stato"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
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
                                                           value=""/>
                                                </div>
                                            </div>
                                        </div>
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
                data-event="bs.productsharehasshopdestination-account.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>