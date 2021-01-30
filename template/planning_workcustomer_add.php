<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'],$page); ?>
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
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Inserisci Attività</h5>
                            </div>
                            <div class="panel-body clearfix">
                                <?php if($billRegistryClientIdSelected!='') : ?>
                                <input type="hidden" id="billRegistryClientIdSelected" name="billRegistryClientIdSelected" value="<?php echo $billRegistryClientIdSelected?>"/>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="billRegistryClientId">Cliente</label>
                                                <select id="billRegistryClientId"
                                                        required="required"
                                                        name="billRegistryClientId"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione il Cliente"
                                                        data-init-plugin="selectize"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="planningWorkTypeId">Seleziona il tipo di attività </label>
                                                <select id="planningWorkTypeId" name="planningWorkTypeId"
                                                        required="required"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona lo il tipo di attività"
                                                        data-init-plugin="selectize">
                                                    <option value="">Seleziona il Tipo di Attività</option>
                                                    <option value="1">Richiesta Post Social</option>
                                                    <option value="2">Segnalazione Anomalia Tecnica</option>
                                                    <option value="3">Richiesta Inserimento saldi</option>
                                                    <option value="4">Richiesta variazione prodotto</option>
                                                    <option value="5">Richiesta modifica Home</option>
                                                    <option value="6">Richiesta Postproduzione immagini</option>
                                                    <option value="7">Richiesta Modifica dettagli</option>
                                                    <option value="8">Richiesta Spedizione manuale</option>
                                                    <option value="9">Altro</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="title">Titolo</label>
                                                <input id="title" class="form-control" type="text" disable
                                                       placeholder="Inserisci il titolo" name="titleEvent"
                                                       value=""
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="startDateWork">Data Inizio Attività</label>
                                                <input type="datetime-local" id="startDateWork" class="form-control"
                                                       placeholder="Data Richiesta"
                                                       name="startDateWork"
                                                       value="<?php echo (new \DateTime())->format('Y-m-d\TH:i'); ?>"
                                                       required="required" disabled>
                                                <input type="hidden" id="endDateWork" name="endDateWork"
                                                       value="<?php echo (new \DateTime())->format('Y-m-d\TH:i'); ?>"/>
                                                <input type="hidden" id="planningWorkStatusId" name="planningWorkStatusId"
                                                       value="1"/>
                                                <input type="hidden" id="solution" name="solution"/>
                                                <input type="hidden" id="percentageStatus" name="percentageStatus"/>
                                                <input type="hidden" id="cost" name="cost" value="0.00" />
                                                <input type="hidden" id="hour" name="hour" value="0.00"/>
                                                <input type="hidden" id="notifyEmail" name="notifyEmail" value="1" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="request">Richiesta</label>
                                                <textarea id="request" cols="60" rows="10"
                                                          placeholder="Inserisci la richiesta"
                                                          name="description"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                <?php else :?>
                                <div class="row">
                                    <div class="col-md-12">
                                        non hai i diritti per caricare una richiesta
                                    </div>
                                </div>
                            <?php endif ?>

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
    <bs-toolbar-group data-group-label="Operazioni su Attività">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-save"
                data-permission="allShops||worker||/admin/product/list"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.post.insert"
                data-title="Salva"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>