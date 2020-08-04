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
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">Aggiungi un post</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="editorialPlanId">Seleziona Piano Editoriale</label>
                                                <select id="editorialPlanId"
                                                        name="editorialPlanId" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione il piano editoriale da utilizzare"
                                                        data-init-plugin="selectize"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="editorialPlanArgumentId">Argomento Evento</label>
                                                <select id="editorialPlanArgumentId"
                                                        name="editorialPlanArgumentId" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione argomento da utilizzare"
                                                        data-init-plugin="selectize"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isVisibleEditorialPlanArgument">Visibile</label>
                                                <input type="checkbox" id="isVisibleEditorialPlanArgument"
                                                       class="form-control"
                                                       placeholder="Visible" checked="true"
                                                       name="isVisibleEditorialPlanArgument" ">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="titleEvent">Titolo Azione Evento</label>
                                                <input id="titleEvent" class="form-control"
                                                       placeholder="Inserisci il titolo" name="titleEvent"
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isEventVisible">Visibile</label>
                                                <input type="checkbox" id="isEventVisible" class="form-control"
                                                       placeholder="Visible" checked="true" name="isEventVisible" ">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="description">Descrizione Evento</label>
                                                <input id="description" class="form-control"
                                                       placeholder="Inserisci la descrizione " name="description" ">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isVisibleDescription">Visibile</label>
                                                <input type="checkbox" id="isVisibleDescription" class="form-control"
                                                       placeholder="Visible" checked="true" name="isVisibleDescription"
                                                ">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <form id="dropzoneModal" class="dropzone" enctype="multipart/form-data"
                                                  id="photoUrl" name="photoUrl" action="POST">
                                                <div class="form-group form-group-default selectize-enabled\
                                                ">
                                                    <label for="file">Immagine Evento</label>
                                                    <div class="fallback">
                                                        <label for="file">Immagine Evento</label>
                                                        <input name="file" type="file" multiple/>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isVisiblePhotoUrl">Visibile</label>
                                                <input type="checkbox" id="isVisiblePhotoUrl" class="form-control"
                                                       placeholder="Visible" checked="true" name="isVisiblePhotoUrl" ">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="bodyEvent">Testo Evento</label>
                                                <textarea id="bodyEvent" cols="150" rows="10" name="bodyEvent"
                                                          placeholder="Inserisci il testo"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isVisibleBodyEvent">Visibile</label>
                                                <input type="checkbox" id="isVisibleBodyEvent" class="form-control"
                                                       placeholder="Visible" checked="true" name="isVisibleBodyEvent"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="note">Note Evento</label>
                                                <textarea id="note" cols="150" rows="10" name="note"
                                                          placeholder="Inserisci le note"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isVisibleNote">Visibile</label>
                                                <input type="checkbox" id="isVisibleNote" class="form-control"
                                                       placeholder="Visible" checked="true" name="isVisibleNote"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="socialPlanId">Seleziona il media da Associare </label>
                                                <select id="socialPlanId"
                                                        required="required"
                                                        name="socialPlanId"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione il media da associare"
                                                        data-init-plugin="selectize"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="status">Seleziona lo Stato</label>
                                                <select id="status" name="status" required="required"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona lo stato"
                                                        data-init-plugin="selectize">
                                                    <option value="new">Stato</option>
                                                    <option value="Draft">Bozza</option>
                                                    <option value="Approved">Approvata</option>
                                                    <option value="Rejected">Rifiutata</option>
                                                    <option value="Published">Pubblicata</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="startEventDate">Data Inizio Evento</label>
                                                <input type="datetime-local" id="startEventDate" class="form-control"
                                                       placeholder="Inserisci la Data di Inizio del Dettaglio"
                                                       name="startEventDate" value=""
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="endEventDate">Data Fine Evento </label>
                                                <input type="datetime-local" id="endEventDate" class="form-control"
                                                       placeholder="Inserisci la Data della Fine del Dettaglio "
                                                       name="endEventDate" value=""
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
                                                    <option value="notNotify">Non Inviare la Notifica</option>
                                                    <option value="yesNotify">Invia la Notifica</option>

                                                </select>
                                            </div>
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

    <?php include "parts/footer.php"; ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni su Post">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="allShops||worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.post.save"
                data-title="Salva Post"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>