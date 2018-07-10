<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
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
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Aggiungi una inserzione </h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="nameInsertion">Nome Inserzione</label>
                                                <input id="nameInsertion" class="form-control"
                                                       placeholder="Inserisci il nome dell'Inserzione" name="nameInsertion"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row oldCampaign">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="campaignId">Seleziona la campagna</label>
                                                <select id="campaignId" name="campaignId"
                                                        class="full-width selectpicker"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row oldCampaign">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="event">Seleziona l'evento</label>
                                                <select id="eventId" name="event"
                                                        class="full-width selectpicker"
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
                <div>
                    <strong>CREA UNA NUOVA CAMPAGNA</strong>
                    <input type="checkbox" id="newCampaign">
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white new-campaign" style="display: none;">
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
                                            <label for="name">Nome Campagna</label>
                                            <input id="nameCampaign"
                                                   placeholder="Inserisci il nome della Campagna"
                                                   name="name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="dateCampaignStart">Data Inizio Campagna</label>
                                            <input type="datetime-local" id="dateCampaignStart"
                                                   placeholder="Inserisci la data di Inizio Campagna"
                                                   name="dateCampaignStart">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="dateCampaignFinish">Data Fine Campagna</label>
                                            <input type="datetime-local" id="dateCampaignFinish"
                                                   placeholder="Inserisci la data di Fine Campagna"
                                                   name="dateCampaignFinish">
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Aggiungi una nuovo evento </h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="nameNewEvent">Nome Evento</label>
                                                <input id="nameNewEvent"
                                                       placeholder="Inserisci il nome della Campagna"
                                                       name="nameNewEvent">
                                            </div>
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
        <bs-toolbar-group data-group-label="Gestione Eventi Newsletter">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-file-o fa-plus"
                    data-permission="AllShops"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.newNewsletterInsertion.save"
                    data-title="Salva Inserzione"
                    data-placement="bottom"
                    data-href="#"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    </bs-toolbar>
</body>
</html>