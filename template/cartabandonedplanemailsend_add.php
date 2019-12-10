<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <!--<script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>-->
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
                                    <h5 class="m-t-10">Crea Pianificazione Mail per Clienti con Carelli Abbandonati</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shopId">Seleziona lo shop su cui applicare al Regola</label>
                                                <select id="shopId" name="shopId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="firstTemplateId">Seleziona Il Template da Utilizzare per il
                                                    Primo Invio</label>
                                                <select id="firstTemplateId" name="firstTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="firstTimeEmailSendDay">Inserisci i Giorni da trascorrere
                                                    prima di eseguire il
                                                    Primo Invio dal Carello abbandonato</label>
                                                <input id="firstTimeEmailSendDay" class="form-control"
                                                       placeholder="Inserisci dopo quanti giorni deve essere inviata la  prima mail"
                                                       name="firstTimeEmailSendDay" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="firstTimeEmailSendHour">Inserisci le ore da trascorrere del
                                                    prima di eseguire il
                                                    Primo Invio dal Carrelo Abbandonato</label>
                                                <input id="firstTimeEmailSendHour" class="form-control"
                                                       placeholder="Inserisci dopo quante ore deve essere inviata la  prima mail name="
                                                       firstTimeEmailSendHour" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="generateCoupon">Vuoi Genera il Coupon per il Primo Invio</label>
                                                <select id="generateCoupon" name="generateCoupon"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        data-init-plugin="selectize"
                                                <option value=""></option>
                                                <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="2">No</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="selectemaildiv"></div>
                                <div id="coupondiv">
                                </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="secondTemplateId">Seleziona Il Template da Utilizzare per il
                                                    Secondo Invio</label>
                                                <select id="secondTemplateId" name="secondTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="secondTimeEmailSendDay">Inserisci i Giorni da trascorrere
                                                    prima di eseguire il
                                                    Secondo Invio dal Carello abbandonato</label>
                                                <input id="secondTimeEmailSendDay" class="form-control"
                                                       placeholder="Inserisci dopo quanti giorni deve essere inviata la  secondas mail"
                                                       name="secondTimeEmailSendDay" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="secondTimeEmailSendHour">Inserisci le ore da trascorrere
                                                    prima di eseguire il
                                                    Secondo Invio dal Carrello Abbandonato</label>
                                                <input id="secondTimeEmailSendHour" class="form-control"
                                                       placeholder="Inserisci dopo quante ore deve essere inviata la  seconda mail name="
                                                       secondTimeEmailSendHour" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="generateCoupon2">Vuoi Generare il Coupon per il Secondo Invio</label>
                                                <select id="generateCoupon2" name="generateCoupon2"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        data-init-plugin="selectize"
                                                <option value=""></option>
                                                <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="2">No</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="selectemaildiv2"></div>
                                    <div id="coupondiv2">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="thirdTemplateId">Seleziona Il Template da Utilizzare per il
                                                    Terzo Invio</label>
                                                <select id="thirdTemplateId" name="thirdTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="thirdTimeEmailSendDay">Inserisci i Giorni da trascorrere
                                                    prima di eseguire il
                                                    Terzo Invio dal Carello abbandonato</label>
                                                <input id="thirdTimeEmailSendDay" class="form-control"
                                                       placeholder="Inserisci dopo quanti giorni deve essere inviata la terza mail"
                                                       name="thirdTimeEmailSendDay" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="thirdTimeEmailSendHour">Inserisci le ore da trascorrere
                                                    prima di eseguire il
                                                    Terzo Invio dal Carrello Abbandonato</label>
                                                <input id="thirdTimeEmailSendHour" class="form-control"
                                                       placeholder="Inserisci dopo quante ore deve essere inviata la  terza mail name="
                                                       thirdTimeEmailSendHour" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="generateCoupon3">Vuoi Generare il Coupon per il Terzo Invio</label>
                                                <select id="generateCoupon3" name="generateCoupon3"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize"
                                                <option value=""></option>
                                                <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="2">No</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="selectemaildiv3"></div>
                                    <div id="coupondiv3">
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
    <bs-toolbar-group data-group-label="Gestione Pianificazione">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="AllShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newPlanSendEmail.save"
                data-title="Salva la Pianificazione"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>