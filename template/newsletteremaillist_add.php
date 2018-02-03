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
                                    <h5 class="m-t-10">Aggiungi Segmento di Pubblico </h5>
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
                                                    <label for="code">Codice Segmento di Pubblico</label>
                                                    <input id="code" class="form-control"
                                                           placeholder="Inserisci il codice del  Segmento di Pubblico"
                                                           name="code" required="required">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="name">Nome Segmento di Pubblico</label>
                                                    <input id="name" class="form-control"
                                                           placeholder="Inserisci il nome del Segmento di Pubblico"
                                                           name="name" required="required">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="filteredField">clicca sui Criteri da Aggiungere al
                                                        Segmento </label>
                                                    <input type="button" id="buttonAge" name="buttonAge" class="button"
                                                           value="Eta"/>
                                                    <input type="button" id="buttonGender" name="buttonGender"
                                                           class="button" value="Sesso"/>
                                                    <input type="button" id="buttonCity" name="buttonCity"
                                                           class="button" value="Citta"/>
                                                    <input type="button" id="buttonCountry" name="buttonCountry"
                                                           class="button" value="Paese"/>
                                                    <input type="button" id="buttonIsActive" name="buttonIsActive"
                                                           class="button" value="Utenti Iscritti"/>
                                                    <input type="button" id="buttonOrder" name="buttonOrder"
                                                           class="button" value="Esclusione Ordini"/>
                                                    <input type="button" id="buttonClean" name="buttonClean"
                                                           class="button" value="Pulisci"/>

                                                </div>
                                            </div>
                                                <div class="col-md-6">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="Operatori">Operatori Logici e di inclusione
                                                        Segmento tra vari Range </label>
                                                    <input type="button" id="buttonOpen" name="buttonOpen" class="button"
                                                           value="( apri Criterio"/>
                                                    <input type="button" id="buttonAnd" name="buttonAnd"
                                                           class="button" value="AND = COMPRESO  "/>
                                                    <input type="button" id="buttonOr" name="buttonOr"
                                                           class="button" value="OR = OPPURE"/>
                                                    <input type="button" id="buttonClose" name="buttonClose"
                                                           class="button" value=") Chiudi Criterio"/>

                                                </div>
                                            </div>
                                        </div>
                                        <div id="inputQuery">
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
                                                    <label for="filterQuery">Costruzione Lista</label>
                                                    <textarea class="form-control" name="filterQuery" id="filterQuery"
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
    <bs-toolbar-group data-group-label="Gestione Segmento di Pubblico">
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