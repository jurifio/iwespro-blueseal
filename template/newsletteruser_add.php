
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
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Aggiungi una Newsletter</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="campaignId">Seleziona la Campagna Associata</label>
                                                <select id="campaignId" name="campaignId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Campagna"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <br><br><br><p><br>
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="user">Nome Newsletter</label>
                                                <input id="name" class="form-control"
                                                 placeholder="Inserisci il nome della newsletter" name="name" required="required">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><br><br>
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="dataDescription">Descrizione Newletter</label>
                                                <input type="text" class="form-control" id="dataDescription" name="dataDescription" value="" />
                                            </div>
                                        </div>
                                    </div>





                                </div>
                            </div>



                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Dettagli Newletter</h5>
                                </div>
                                <div class="panel-body clearfix">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="sendAddressDate">Data di Invio</label>
                                                <input type="date" class="form-control" id="sendAddressDate" name="sendAddressDate" value="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="fromEmailAddressId">no-reply@.pickyshop.com</label>
                                                <input type="hidden" class="form-control" id="fromEmailAddressId" name="fromEmailAddressId" value="1" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="newsletterEmailListId">Seleziona la lista dei destinatari</label>
                                                <select id="newsletterEmailListId" name="newsletterEmailListId"
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
                                                <label for="subject">Oggetto</label>
                                                <input type="text" class="form-control" id="subject" name="subject" value="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="newsletterTemplateId">Seleziona Il Template da Utilizzare</label>
                                                <select id="newsletterTemplateId" name="NewletterTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>

                                            </div>
                                            <p>
                                        </div>
                                    </div>



                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="preCompiledTemplate1">Template Predefinito</label>
                                            <input type="file" class="form-control" id="preCompiledTemplate1" name="preCompiledTemplate1" value="" />
                                            <input type="hidden" class="form-control" id="preCompiledTemplate" name="preCompiledTemplate" value="" />
                                            <pre id="file-content"></pre>
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
            data-icon="fa-file-o fa-plus"
            data-permission="AllShops"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.newNewsletterUser.save"
            data-title="Salva la  Newsletter"
            data-placement="bottom"
            data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>