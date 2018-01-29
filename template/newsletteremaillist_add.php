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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Aggiungi Filtro  </h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="newsletterGroupId">Seleziona Il Gruppo
                                                </label>
                                                <select id="newsletterGroupId" name="newsletterGroupId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="name">Codice Gruppo</label>
                                                <input id="name" class="form-control"
                                                       placeholder="Inserisci il nome del Gruppo"
                                                       name="name" required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="filteredField">Seleziona la tipologia di filtro per il  Gruppo</label>
                                                <select id="filteredField" name="filteredField"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Colonna"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="inputGender">
                                    </div>
                                    <div id="inputAge">

                                    </div>
                                    <div id="inputCity">

                                    </div>
                                    <div id="inputCountry">

                                    </div>
                                    <div id="inputIsActive">
                                    </div>
                                    <div id="inputIsActive">
                                    </div>
                                    <div id="inputOrderDate">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="filteAge">Costruzione Filtro Età</label>
                                                <textarea class="form-control" name="filterAge" id="filterAge"
                                                          value=""></textarea>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="filteCity">Costruzione Filtro Città</label>

                                                <textarea class="form-control" name="filterCity" id="filterCity"
                                                          value=""></textarea>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="filteCountry">Costruzione Filtro Nazione</label>
                                                <textarea class="form-control" name="filterCountry" id="filterCountry"
                                                          value=""></textarea>

                                            </div>
                                        </div>
                                    </div>

                                    </div>


                                    <div class="row">
                                    </div>

                                </div>
                            </div>


                        </div>

                </form>
            </div>
        </div>
    </div>

    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Gruppo">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="AllShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newNewsletterEmailList.save"
                data-title="Salva il Gruppo"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>