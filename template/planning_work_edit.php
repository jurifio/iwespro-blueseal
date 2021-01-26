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
            <input type="hidden" id="planningWorkId"
                   name="planningWorkId"
                   value="<?php echo $pWork->id; ?>"/>
            <input type="hidden" id="billRegistryClientIdSelected" name="billRegistryClientIdSelected"
                   value="<?php echo $pWork->billRegistryClientId; ?>"/>
            <input type="hidden" id="planningWorkStatusIdSelected" name="planningWorkStatusIdSelected"
                   value="<?php echo $pWork->planningWorkStatusId; ?>"/>
            <input type="hidden" id="planningWorkTypeIdSelected" name="planningWorkTypeIdSelected"
                   value="<?php echo $pWork->planningWorkTypeId; ?>"/>
            <input type="hidden" id="userId" name="userId" value="<?php echo \Monkey::app()->getUser()->getId(); ?>"/>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Modifica Attività</h5>
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
                                                   value="<?php echo $pWork->title ?>"
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
                                                <?php if ($pWork->notifyEmail == 1) {
                                                    echo '<option value="0" >Non Inviare la Notifica</option>';
                                                    echo '<option value="1" selected="selected">Invia la Notifica</option>';
                                                } else {
                                                    echo '<option value="0" selected="selected">Non Inviare la Notifica</option>';
                                                    echo '<option value="1" >Invia la Notifica</option>';
                                                } ?>

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
                                                   value="<?php echo (new \DateTime($pWork->startDateWork))->format('Y-m-d\TH:i'); ?>"
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="endDateWork">Data Fine Attività </label>
                                            <input type="datetime-local" id="endDateWork" class="form-control"
                                                   placeholder="Inserisci la Data della Fine"
                                                <?php echo (new \DateTime($pWork->endDateWork))->format('Y-m-d\TH:i'); ?>
                                                   name="endDateWork"
                                                   value="<?php echo (new \DateTime($pWork->endDateWork))->format('Y-m-d\TH:i'); ?>"
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
                                            <textarea id="request" cols="60" rows="10"
                                                      placeholder="Inserisci la richiesta"
                                                      name="description"><?php echo $pWork->request ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="solution">Soluzione</label>
                                            <textarea id="solution" cols="60" rows="10"
                                                      placeholder="Inserisci la soluzione"
                                                      name="description"><?php echo $pWork->solution ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="percentageStatus">% di Completamento</label>
                                                <select id="percentageStatus" name="percentageStatus"
                                                        required="required"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la percentuale"
                                                        data-init-plugin="selectize">
                                                    <?php switch ($pWork->percentageStatus) {
                                                        case '0':
                                                            echo '<option selected="selected" value="0">0%</option>';
                                                            echo '<option value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option value="60">60%</option>';
                                                            echo '<option value="70">70%</option>';
                                                            echo '<option value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '10':
                                                            echo '<option  value="0">0%</option>';
                                                            echo '<option  selected="selected" value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option value="60">60%</option>';
                                                            echo '<option value="70">70%</option>';
                                                            echo '<option value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '20':
                                                            echo '<option  value="0">0%</option>';
                                                            echo '<option  value="10">10%</option>';
                                                            echo '<option  selected="selected"  value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option value="60">60%</option>';
                                                            echo '<option value="70">70%</option>';
                                                            echo '<option value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '30':
                                                            echo '<option  value="0">0%</option>';
                                                            echo '<option  value="10">10%</option>';
                                                            echo '<option   value="20">20%</option>';
                                                            echo '<option selected="selected"  value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option value="60">60%</option>';
                                                            echo '<option value="70">70%</option>';
                                                            echo '<option value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '40':
                                                            echo '<option value="0">0%</option>';
                                                            echo '<option value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option selected="selected" value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option value="60">60%</option>';
                                                            echo '<option value="70">70%</option>';
                                                            echo '<option value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '50':
                                                            echo '<option value="0">0%</option>';
                                                            echo '<option value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option selected="selected" value="50">50%</option>';
                                                            echo '<option value="60">60%</option>';
                                                            echo '<option value="70">70%</option>';
                                                            echo '<option value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '60':
                                                            echo '<option value="0">0%</option>';
                                                            echo '<option value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option selected="selected" value="60">60%</option>';
                                                            echo '<option value="70">70%</option>';
                                                            echo '<option value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '70':
                                                            echo '<option value="0">0%</option>';
                                                            echo '<option value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option  value="60">60%</option>';
                                                            echo '<option selected="selected" value="70">70%</option>';
                                                            echo '<option value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '80':
                                                            echo '<option value="0">0%</option>';
                                                            echo '<option value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option  value="60">60%</option>';
                                                            echo '<option  value="70">70%</option>';
                                                            echo '<option  selected="selected" value="80">80%</option>';
                                                            echo '<option value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '90':
                                                            echo '<option value="0">0%</option>';
                                                            echo '<option value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option  value="60">60%</option>';
                                                            echo '<option  value="70">70%</option>';
                                                            echo '<option  value="80">80%</option>';
                                                            echo '<option   selected="selected" value="90">90%</option>';
                                                            echo '<option value="100">100%</option>';
                                                            break;
                                                        case '100':
                                                            echo '<option value="0">0%</option>';
                                                            echo '<option value="10">10%</option>';
                                                            echo '<option value="20">20%</option>';
                                                            echo '<option value="30">30%</option>';
                                                            echo '<option value="40">40%</option>';
                                                            echo '<option value="50">50%</option>';
                                                            echo '<option  value="60">60%</option>';
                                                            echo '<option  value="70">70%</option>';
                                                            echo '<option  value="80">80%</option>';
                                                            echo '<option   value="90">90%</option>';
                                                            echo '<option selected="selected" value="100">100%</option>';
                                                            break;

                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="hour">Ore lavorate</label>
                                            <input id="hour" class="form-control" type="text"
                                                   name="hour" value="<?php echo $pWork->hour; ?>"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="cost">Costo</label>
                                            <input id="cost" class="form-control" type="text"
                                                   name="cost" value="<?php echo $pWork->cost ?>"
                                                   placeholder="Inserisci il prezzo"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="total">Totale</label>
                                            <input id="total" class="form-control" type="text"
                                                   value="<?php echo number_format(($pWork->hour * $pWork->cost),'2','.','') ?>"
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
    <bs-toolbar-group data-group-label="Operazioni su Post">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-save"
                data-permission="allShops||worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.post.update"
                data-title="Salva Post"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>