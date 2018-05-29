<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables', 'charts'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <!-- <script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>-->
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Modifica Inserzione Newsletter</h5>
                                    <input type ="hidden" name="newsletterId"
                                           id="newsletterId"
                                           value="<?php echo $newsletter->id;?>"/>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="filteredField">Seleziona se Creare una Campagna o selezionarla</label>
                                                <select id="filteredField" name="filteredField"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona l\'opzione"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="inputCampaign">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="campaignName">Campagna</label>
                                                    <select id="campaignName" name="campaignName"  class="full-width selectpicker"
                                                            placeholder="Seleziona la Lista"
                                                            data-init-plugin="selectize">
                                                        <option value="<?php echo $newsletterCampaign->id;?>"><?php echo $newsletterCampaign->name;?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="dateCampaignStart">Data Inizio Campagna:<?php echo $newsletterCampaign->dateCampaignStart;?></label>
                                                    <input type="datetime" name="dateCampaignStart" value="<?php echo $newsletterCampaign->dateCampaignStart;?>"/>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="dateCampaignFinish">Data Fine Campagna:<?php echo $newsletterCampaign->dateCampaignFinish;?></label>
                                                    <input type="datetime" name="dateCampaignFinish" value="<?php echo $newsletterCampaign->dateCampaignFinish;?>"/>

                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div id="inputEvent">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="newsletterEventName">Evento della Campagna</label>
                                                    <select id="newsletterEventName" name="newsletterEventName"  class="full-width selectpicker"
                                                            placeholder="Seleziona la Lista"
                                                            data-init-plugin="selectize">
                                                        <option value="<?php echo $newsletterEvent->id;?>"><?php echo $newsletterEvent->name;?>
                                                        </option>


                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <br><br><br><p><br>
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="name">Nome Newsletter</label>
                                                <input id="name" class="form-control" value="<?php echo $newsletter->name;?>"
                                                       placeholder="Inserisci il nome della newsletter" name="name" required="required">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><br><br>
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="dataDescription">Descrizione Newletter</label>
                                                <input type="text" class="form-control" id="dataDescription" name="dataDescription"  value="<?php echo $newsletter->dataDescription;?>" />
                                            </div>
                                        </div>
                                    </div>





                                </div>
                            </div>



                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Dettagli Newsletter</h5>
                                </div>
                                <div class="panel-body clearfix">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="sendAddressDate">Data di Invio</label>
                                                <input type="datetime-local" class="form-control" id="sendAddressDate" name="sendAddressDate" value="<?php echo $newsletter->sendAddressDate;?>" />
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
                                    <div id="inputNewsletterEmailList">
                                        <input type ="hidden" name="newsletterEmailListId"
                                               id="newsletterEmailListId"
                                               value="<?php echo $newsletterEmailList->id;?>"/>
                                        <div class="row">
                                            <div class="col-md-12">

                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="newsletterEmailListIdPrev">Seleziona la lista dei destinatari</label>

                                                    <select id="newsletterEmailListIdPrev" name="newsletterEmailListIdPrev"
                                                            class="full-width selectpicker"
                                                            placeholder="Seleziona la Lista"
                                                            data-init-plugin="selectize">
                                                        <option value="<?php echo $newsletterEmailList->id;?>"><?php echo $newsletterEmailList->name;?></option>
                                                        <option value="new">Seleziona un altro Gruppo</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="subject">Oggetto</label>
                                                <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $newsletter->subject;?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="newsletterTemplateId">Seleziona Il Template da Utilizzare</label>
                                                <select id="newsletterTemplateId" name="newsletterTemplateId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">


                                                </select>

                                            </div>                                            <p>
                                        </div>
                                    </div>
                                    <div id="inputNewsletterTemplateId">

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div align="center"

                                        <label for="preCompiledTemplate1">Template Utilizzato</label>

                                        <textarea id="preCompiledTemplate1" name="preCompiledTemplate1" data-json="PostTranslation.content" rows="1000" value="<?php echo $newsletter->preCompiledTemplate;?>"></textarea>
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
    <bs-toolbar-group data-group-label="Gestione Newsletter">
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
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-envelope-o"
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