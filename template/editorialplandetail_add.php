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
                                        <div id="divSelecterCampaign" class="hide">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="selecterCampaign">Seleziona Operazione su </label>
                                                <select id="selecterCampaign"
                                                        name="selecterCampaign" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione operazioni su campagna da utilizzare"
                                                        data-init-plugin="selectize">
                                                    <option value="">seleziona</option>
                                                    <option value="0">Crea Nuova</option>
                                                    <option value="1">Seleziona Esistente</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="divCampaign" class="hide">

                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="titleEvent">Titolo Azione Evento</label>
                                            <input id="titleEvent" class="form-control"
                                                   placeholder="Inserisci il titolo" name="titleEvent"
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isEventVisible">Visibile</label>
                                            <input type="checkbox" id="isEventVisible" class="form-control"
                                                   placeholder="Visible" checked="true" name="isEventVisible"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="description">Descrizione Evento</label>
                                            <textarea id="description" cols="60" rows="10"
                                                      placeholder="Inserisci la descrizione dell'evento"
                                                      name="description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisibleDescription">Visibile</label>
                                            <input type="checkbox" id="isVisibleDescription" class="form-control"
                                                   placeholder="Visible" checked="true" name="isVisibleDescription">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="bodyEvent">Testo Evento</label>
                                            <textarea id="bodyEvent" cols="180" rows="10" name="bodyEvent"
                                                      placeholder="Inserisci il testo dell'evento max 180 caratteri per riga"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="linkDestination">Link Destinazione</label>
                                            <textarea id="linkDestination" cols="180" rows="1"
                                                      placeholder="Inserisci  i link di destinazione"
                                                      name="linkDestination"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisibleBodyEvent">Visibile</label>
                                            <input type="checkbox" id="isVisibleBodyEvent" class="form-control"
                                                   placeholder="Visible" checked="true" name="isVisibleBodyEvent"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <form id="dropzoneModal" class="dropzone" enctype="multipart/form-data"
                                              id="photoUrl" name="photoUrl" action="POST">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="file">Immagine Evento max 6</label>
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisiblePhotoUrl">Visibile</label>
                                            <input type="checkbox" id="isVisiblePhotoUrl" class="form-control"
                                                   placeholder="Visible" checked="true" name="isVisiblePhotoUrl"/>
                                        </div>
                                    </div>
                                </div>
                               <div class="row">
                                   <div class="col-md-4">
                                       <div class="form-group form-group-default selectize-enabled">
                                           <label for="imageTitle1">Titolo Immagine1</label>
                                           <textarea id="imageTitle1" class="form-control"
                                                  placeholder="Inserisci il titolo per l'immagine 1" name="imageTitle1"
                                           ></textarea>
                                       </div>
                                   </div>
                                   <div class="col-md-4">
                                       <div class="form-group form-group-default selectize-enabled">
                                           <label for="descriptionImage1">Descrizione Immagine1</label>
                                           <textarea id="descriptionImage1" class="form-control"
                                                  placeholder="Inserisci la descrizione per l'immagine 1" name="descriptionImage1"
                                           ></textarea>
                                       </div>
                                   </div>
                                   <div class="col-md-4">
                                       <div class="form-group form-group-default selectize-enabled">
                                           <label for="imageUrl1">link Immagine1</label>
                                           <textarea id="imageUrl1" class="form-control"
                                                     placeholder="Inserisci il link per l'immagine 1" name="imageUrl1"
                                           ></textarea>
                                       </div>
                                   </div>
                               </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageTitle2">Titolo Immagine2</label>
                                            <textarea id="imageTitle2" class="form-control"
                                                      placeholder="Inserisci il titolo per l'immagine 1" name="imageTitle2"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="descriptionImage2">Descrizione Immagine2</label>
                                            <textarea id="descriptionImage2" class="form-control"
                                                      placeholder="Inserisci la descrizione per l'immagine 2" name="descriptionImage1"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageUrl2">link Immagine2</label>
                                            <textarea id="imageUrl2" class="form-control"
                                                      placeholder="Inserisci il link per l'immagine 2" name="imageUrl2"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageTitle3">Titolo Immagine3</label>
                                            <textarea id="imageTitle3" class="form-control"
                                                      placeholder="Inserisci il titolo per l'immagine 3" name="imageTitle3"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="descriptionImage3">Descrizione Immagine3</label>
                                            <textarea id="descriptionImage3" class="form-control"
                                                      placeholder="Inserisci la descrizione per l'immagine 3" name="descriptionImage3"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageUrl3">link Immagine3</label>
                                            <textarea id="imageUrl3" class="form-control"
                                                      placeholder="Inserisci il link per l'immagine 3" name="imageUrl3"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageTitle4">Titolo Immagine4</label>
                                            <textarea id="imageTitle4" class="form-control"
                                                      placeholder="Inserisci il titolo per l'immagine 4" name="imageTitle4"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="descriptionImage4">Descrizione Immagine4</label>
                                            <textarea id="descriptionImage4" class="form-control"
                                                      placeholder="Inserisci la descrizione per l'immagine 4" name="descriptionImage4"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageUrl4">link Immagine4</label>
                                            <textarea id="imageUrl4" class="form-control"
                                                      placeholder="Inserisci il link per l'immagine 4" name="imageUrl4"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageTitle5">Titolo Immagine5</label>
                                            <textarea id="imageTitle5" class="form-control"
                                                      placeholder="Inserisci il titolo per l'immagine 5" name="imageTitle5"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="descriptionImage5">Descrizione Immagine5</label>
                                            <textarea id="descriptionImage5" class="form-control"
                                                      placeholder="Inserisci la descrizione per l'immagine 5" name="descriptionImage5"
                                                      ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageUrl5">link Immagine5</label>
                                            <textarea id="imageUrl5" class="form-control"
                                                      placeholder="Inserisci il link per l'immagine 5" name="imageUrl5"></textarea>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageTitle6">Titolo Immagine6</label>
                                            <textarea id="imageTitle6" class="form-control"
                                                      placeholder="Inserisci il titolo per l'immagine 6" name="imageTitle6"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="descriptionImage6">Descrizione Immagine6</label>
                                            <textarea id="descriptionImage6" class="form-control"
                                                      placeholder="Inserisci la descrizione per l'immagine 6" name="descriptionImage6"
                                                      required="required"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="imageUrl6">link Immagine6</label>
                                            <textarea id="imageUrl6" class="form-control"
                                                      placeholder="Inserisci il link per l'immagine 6" name="imageUrl6"
                                            ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="video1">link Video1</label>
                                            <textarea id="video1" class="form-control"
                                                      placeholder="Inserisci il link per il video 1" name="video1"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="video2">link Video2</label>
                                            <textarea id="video2" class="form-control"
                                                      placeholder="Inserisci il link per il video 2" name="video2"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="video3">link Video3</label>
                                            <textarea id="video3" class="form-control"
                                                      placeholder="Inserisci il link per video 3" name="video3"
                                            ></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="video4">link Video4</label>
                                            <textarea id="video4" class="form-control"
                                                      placeholder="Inserisci il link per il video 4" name="video4"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="video5">link Video5</label>
                                            <textarea id="video5" class="form-control"
                                                      placeholder="Inserisci il link per il video 5" name="video5"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="video6">link Video6</label>
                                            <textarea id="video6" class="form-control"
                                                      placeholder="Inserisci il link per video 6" name="video6"
                                            ></textarea>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="note">Note Evento</label>
                                            <textarea id="note" cols="180" rows="10" name="note"
                                                      placeholder="Inserisci le note"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
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