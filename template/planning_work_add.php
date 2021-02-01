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
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="billRegistryClientId">Seleziona il Cliente </label>
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
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="title">Titolo</label>
                                            <input id="title" class="form-control" type="text"
                                                   placeholder="Inserisci il titolo" name="titleEvent"
                                                   value=""
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="notifyEmail">Notificare al Cliente</label>
                                            <select id="notifyEmail" name="notifyEmail" required="required"
                                                    class="full-width selectpicker"
                                                    placeholder="Seleziona"
                                                    data-init-plugin="selectize">
                                                <option value="0">Non Inviare la Notifica</option>
                                                <option value="1" selected="selected">Invia la Notifica</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="startDateWork">Data Inizio Attività</label>
                                            <input type="datetime-local" id="startDateWork" class="form-control"
                                                   placeholder="Inserisci la Data di Inizio "
                                                   name="startDateWork"
                                                   value="<?php echo (new \DateTime())->format('Y-m-d\TH:i'); ?>"
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="endDateWork">Data Fine Attività </label>
                                            <input type="datetime-local" id="endDateWork" class="form-control"
                                                   placeholder="Inserisci la Data della Fine"
                                                   name="endDateWork"
                                                   value="<?php echo (new \DateTime())->format('Y-m-d\TH:i'); ?>"
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="planningWorkStatusId">Seleziona lo Stato</label>
                                            <select id="planningWorkStatusId"
                                                    name="planningWorkStatusId" class="full-width selectpicker"
                                                    required="required"
                                                    placeholder="Seleziona lo Stato"
                                                    data-init-plugin="selectize"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="request">Richiesta</label>
                                            <textarea  class="summer" id="request" cols="180" rows="50"
                                                      placeholder="Inserisci la richiesta"
                                                      name="description"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="solution">Soluzione</label>
                                            <textarea class="summer" id="solution" cols="180" rows="50"
                                                      placeholder="Inserisci la soluzione"
                                                      name="solution"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="percentageStatus">% di Completamento</label>
                                            <select id="percentageStatus" name="percentageStatus"
                                                    required="required"
                                                    class="full-width selectpicker"
                                                    placeholder="Seleziona lo il tipo di attività"
                                                    data-init-plugin="selectize">
                                                <option value="0">0%</option>
                                                <option value="10">10%</option>
                                                <option value="20">20%</option>
                                                <option value="30">30%</option>
                                                <option value="40">40%</option>
                                                <option value="50">50%</option>
                                                <option value="60">60%</option>
                                                <option value="70">70%</option>
                                                <option value="80">80%</option>
                                                <option value="90">90%</option>
                                                <option value="100">100%</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="hour">Ore lavorate</label>
                                            <input id="hour" class="form-control" type="text"
                                                   name="hour" value="0"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="cost">Costo</label>
                                            <input id="cost" class="form-control" type="text"
                                                   name="cost" value="0"
                                                   placeholder="Inserisci il prezzo"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="total">Totale</label>
                                            <input id="total" class="form-control" type="text"
                                                   value=""
                                                   name="total" placeholder="totale"/>
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
    <bs-toolbar-group data-group-label="Operazioni su Attività">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-save"
                data-permission="allShops||worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.post.insert"
                data-title="Salva"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>