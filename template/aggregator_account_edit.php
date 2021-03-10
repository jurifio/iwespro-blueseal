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
                            <button class="tablinks" onclick="openTab(event, 'insertRules')">Brand</button>
                            <button class="tablinks" onclick="openTab(event, 'insertCampaign')">Campagna
                            </button>
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
                                                <input type="hidden" id="marketplaceAccountId"
                                                       name="marketplaceAccountId"
                                                       value="<?php echo $marketplaceCode[0]; ?>">
                                                <input type="hidden" id="marketplaceId"
                                                       name="marketplaceId"
                                                       value="<?php echo $marketplaceCode[1]; ?>">
                                                <input id="marketplace_account_name" autocomplete="off" type="text"
                                                       class="form-control" name="marketplace_account_name"
                                                       value="<?php echo $marketplaceAccount->name; ?>"
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
                                                    <option value="<?php echo $marketplaceAccount->config['lang']; ?>"><?php echo $marketplaceAccount->config['lang'] ?></option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default required">
                                                <label for="slug">Slug</label>
                                                <input id="slug" autocomplete="off" type="text"
                                                       class="form-control" name="slug"
                                                       value="<?php echo $marketplaceAccount->config['slug'] ?>"
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-primary" name="uploadLogo"
                                                    id="uploadLogo">carica Logo
                                            </button>
                                            <input id="logoFile" type="hidden"
                                                   value="<?php echo $marketplaceAccount->config['logoFile'] ?>"/>
                                            <div id="returnFileLogo"><img src="<?php echo $marketplaceAccount->config['logoFile'] ?>"/>
                                            </div>
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
                                                    <?php if ($marketplaceAccount->isActive == "1") {
                                                        echo '<option  selected="selected" value="1">Si</option>';
                                                        echo '<option value="0">No</option>';
                                                    } else {
                                                        echo '<option  value="1">Si</option>';
                                                        echo '<option  selected="selected" value="0">No</option>';
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <label for="nameAdminister">Account Manager</label>
                                                <input id="nameAdminister" autocomplete="off" type="text"
                                                       class="form-control" name="nameAdminister"
                                                       value="<?php echo $marketplaceAccount->config['nameAdminister'] ?>"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <label for="emailNotify">Email Notifica </label>
                                                <input id="emailNotify" autocomplete="off" type="text"
                                                       class="form-control" name="emailNotify"
                                                       value="<?php echo $marketplaceAccount->config['emailNotify'] ?>"
                                                       required="required"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default required">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="aggregatorHasShopId">Seleziona aggregatore Account
                                                    </label>
                                                    <select id="aggregatorHasShopId"
                                                            name="aggregatorHasShopId"
                                                            class="full-width selectpicker"
                                                            placeholder="Seleziona aggregatore Account"
                                                            data-init-plugin="selectize">
                                                        <?php echo $optionAggregator;?>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="typeInsertion" name="typeInsertion" value="2">
                                    <input type="hidden" id="marketplaceName" name="marketplaceName" value="<?php echo $marketplaceAccount->marketplaceId?>"/>
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
                                                       class="form-control" name="nameRule" value="<?php echo $marketplaceAccount->config['nameRule']?>"
                                                       required="required"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="rawBrands">
                                        <input type="hidden" id="ruleOption" name="ruleOption" value="<?php echo $marketplaceAccount->config['ruleOption']?>"/>;
                                        <?php echo $bodyres?>
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
                                    <div class="row" id="divcampaign">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="campaignName">Associata
                                                    Campagna
                                                    </label>
                                                <select id="campaignName"
                                                        name="campaignName"
                                                        class="full-width selectpicker"
                                                        placeholder=""
                                                        data-init-plugin="selectize">
                                                    <?php echo $campaignOption ?>
                                                </select>
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
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni Aggregatore">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.aggregator-account.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
</bs-toolbar>
</body>
</html>