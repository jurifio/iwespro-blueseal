<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
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
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Aggiungi una Campagna </h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="nameShop">Mittente della campagna</label>
                                                <select name="nameShop" id="nameShop">
                                                    <option disabled selected value>Seleziona un mittente</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="name">Nome Campagna</label>
                                                <input id="name" class="form-control"
                                                       placeholder="Inserisci il nome della Campagna" name="name"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="dateCampaignStart">Data Inizio Campagna</label>
                                                <input type="datetime-local" id="dateCampaignStart" class="form-control"
                                                       placeholder="Inserisci la data di Inizio Campagna" name="dateCampaignStart"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="dateCampaignFinish">Data Fine Campagna</label>
                                                <input type="datetime-local" id="dateCampaignFinish" class="form-control"
                                                       placeholder="Inserisci la data di Fine Campagna" name="dateCampaignFinish"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>

                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <?php include "parts/footer.php"; ?>
    </div>
    <?php include "parts/bsmodal.php"; ?>
    <?php include "parts/alert.php"; ?>
    <bs-toolbar class="toolbar-definition">
        <bs-toolbar-group data-group-label="Gestione Campagne Newsletter">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-file-o fa-plus"
                    data-permission="allShops||worker"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.newNewsletterCampaign.save"
                    data-title="Salva la Campagna"
                    data-placement="bottom"
                    data-href="#"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    </bs-toolbar>
</body>
</html>