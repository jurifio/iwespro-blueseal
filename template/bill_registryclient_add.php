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
                            <button class="tablinks" onclick="openTab(event, 'insertClient')">Dati Cliente</button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientBillingInfo')">Dati Amministrativi</button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientLocation')">Sedi e Filiali</button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientContact')">Contatti</button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientProduct')">Servizi Associati</button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientContract')">Contratti</button>

                        </div>
                    </div>
                </div>
                <div id="insertClient" class="tabcontent">
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
                                                <label for="companyName">Nome Cliente</label>
                                                <input id="companyName" autocomplete="off" type="text"
                                                       class="form-control" name="companyName" value=""
                                                       required="required"/>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled required">
                                                <label for="countryId">Seleziona la Nazione </label>
                                                <select id="countryId" name="countryId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="insertClientBillingInfo" class="tabcontent">
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
                <div id="insertClientLocation" class="tabcontent">
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
                <div id="insertClientContact" class="tab-content"></div>
                <div id="insertClientProduct" class="tab-content"></div>
                <div id="insertClientContract" class="tab-content"></div>


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