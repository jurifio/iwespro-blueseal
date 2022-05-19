<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
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
                <div class="panel panel-transparent">
                    <div class="panel-heading">
                        <h5>Inserisci un Banner</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="on">
                            <div class="row clearfix">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default required">
                                        <label for="name">Nome Banner</label>
                                        <input type="text" class="form-control" id="name" name="name" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default required">
                                        <label for="position">Posizione sul Catalogo</label>
                                        <input type="text" class="form-control" id="position" name="position" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default required">
                                        <label for="link">link</label>
                                        <input type="text" class="form-control" id="link" name="link" required/>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-group-default">
                                        <button type="button" class="btn btn-primary" name="uploadLogo"
                                                id="uploadLogo">carica Banner L281 X H374
                                        </button>
                                        <input id="textHtml" type="hidden" value=""/>
                                        <div id="returnFileLogo"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-sm-3">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="remoteShopId">Shop Di Destinazione</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona Lo Shop"
                                                tabindex="-1" title="Seleziona la Shop"
                                                name="remoteShopId" id="remoteShopId">
                                        </select>
                                    </div>
                                </div>
                                <div id="divCampaignType" class="col-sm-6 hide">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="campaignId">Campagna</label>
                                        <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona la Campagna" data-init-plugin="selectize"
                                                tabindex="-1" title="campaignId" name="campaignId"
                                                id="campaignId">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group form-group-default">
                                        <label for="isActive">Stato Attivo</label>
                                        <select class="full-width selectpicker"
                                                placeholder="Seleziona"
                                                tabindex="-1" title="Seleziona"
                                                name="isActive" id="isActive">
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
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
    <bs-toolbar-group data-group-label="Operazioni">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/marketing"
                data-event="bs.banner.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
        </bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>