<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <!--<script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>-->
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=z3tiwzxrspg36g21tiusdfsqt9f27isw6547l88aw19e0qej"></script>
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
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Dettagli Newsletter</h5>
                                </div>
                                <div class="panel-body clearfix">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="sendAddressDate">Data di Invio</label>
                                                <input type="datetime-local" class="form-control" id="sendAddressDate"
                                                       name="sendAddressDate" value=""/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="fromEmailAddressId">Email Shop Mittente
                                                </label>
                                                <select id="fromEmailAddressId" name="fromEmailAddressId"Select
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona lo Shop di Invio"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="subject">Oggetto</label>
                                                <input type="text" class="form-control" id="subject" name="subject"
                                                       value=""/>
                                            </div>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="emailToSelect">Seleziona i Destinatari da Iwes
                                                   </label>
                                                <select id="emailToSelect" name="emailToSelect"Select
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona i Destinatari"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="emailTo">Destinatari Selezionati
                                                    </label>
                                                    <input type="text" class="form-control" id="emailTo" name="emailTo"
                                                    </select>
                                                </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="newsletterTemplateId">Seleziona Il Template da
                                                    Utilizzare</label>
                                                <select id="newsletterTemplateId" name="NewsletterTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label>Template Predefinito</label>
                                                <textarea class="form-control z-depth-1"   id="preCompiledTemplate1" name="preCompiledTemplate1" rows="100" placeholder="Seleziona il Template "
                                                          data-json="PostTranslation.content"></textarea>
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

    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione NewsletterUser">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-envelope-o"
                data-permission="AllShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newEmailUser.save"
                data-title="Salva la  Newsletter"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-check"
                data-permission="AllShops"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newNewsletterUser.sendTest"
                data-title="Invia il test per  la Newsletter"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>