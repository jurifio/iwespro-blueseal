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
            <input type="hidden" id="editorialPlanDetailIdSelected"
                   name="editorialPlanDetailIdSelected"
                   value="<?php echo $editorialPlanDetail->id; ?>"/>
            <input type="hidden" id="facebookCampaignId" name="facebookCampaignId"
                   value="<?php echo $editorialPlanDetail->facebookCampaignId; ?>"/>
            <input type="hidden" id="groupInsertionId" name="groupInsertionId"
                   value="<?php echo $editorialPlanDetail->groupInsertionId; ?>"/>
            <input type="hidden" id="editorialPlanIdSelected"  name="editorialPlanIdSelected"
                   value="<?php echo $editorialPlanDetail->editorialPlanId; ?>"/>
            <input type="hidden" id="socialPlanIdSelected" name="socialPlanIdSelected"
                   value="<?php echo $editorialPlanDetail->socialId; ?>"/>
            <input type="hidden" id="editorialPlanArgumentIdSelected"
                   name="editorialPlanArgumentIdSelected"
                   value="<?php echo $editorialPlanDetail->editorialPlanArgumentId; ?>"/>
            <input type="hidden" id="insertionId" name="insertionId"
                   value="<?php echo $editorialPlanDetail->insertionId; ?>"/>
            <input type="hidden" id="creativeId" name="creativeId"
                   value="<?php echo $editorialPlanDetail->creativeId; ?>"/>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default clearfix">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-10">Modifica il post</h5>
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
                                            <?php if ($editorialPlanDetail->isVisibleEditorialPlanArgument == 1) {
                                                $ischecked = ' checked="true"';
                                            } else {
                                                $ischecked = '';
                                            } ?>
                                            <input type="checkbox" <?php echo $ischecked ?>
                                                   id="isVisibleEditorialPlanArgument"
                                                   class="form-control"
                                                   placeholder="Visible"
                                                   name="isVisibleEditorialPlanArgument" ">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div id="divSelecterCampaign">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="selecterCampaign">Seleziona Operazione su </label>
                                                <select id="selecterCampaign"
                                                        name="selecterCampaign" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione operazioni su campagna da utilizzare"
                                                        data-init-plugin="selectize">
                                                    <option value="">seleziona</option>
                                                    <option value="0">Crea Nuova</option>
                                                    <option value="1" selected="selected">Esistente</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="divCampaign" class="show">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="campaignName">Seleziona Campagna</label>
                                                <select id="campaignName"
                                                        name="campaignName" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione campagna da utilizzare"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="groupAdsName">Seleziona il Gruppo Inserzioni</label>
                                                <select id="groupAdsName"
                                                        name="groupAdsName" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione il Gruppo inserzioni"
                                                        data-init-plugin="selectize">
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="buying_type">Tipo di Acquisto</label>
                                                <select id="buying_type"
                                                        name="buying_type" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione campagna da utilizzare"
                                                        data-init-plugin="selectize">
                                                    <?php if($editorialPlanDetail->buying_type=='AUCTION'){
                                                        echo '<option selected="selected" value="AUCTION">Asta</option>
                                                    <option value="RESERVED">Copertura e Frequenza</option>';
                                                    }else{
                                                        echo '<option  value="AUCTION">Asta</option>
                                                    <option  selected="selected" value="RESERVED">Copertura e Frequenza</option>';
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="objective">Obiettivo della Campagna</label>
                                                <select id="objective"
                                                        name="objective" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione campagna da utilizzare"
                                                        data-init-plugin="selectize">
                                                    <?php switch ($EditorialPlanDetail->objective){
                                                        case 'BRAND_AWARENESS':
                                                       echo '<option selected="selected" value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option value="REACH">Copertura</option>
                                                    <option value="LOCAL_AWARENESS">Traffico</option>
                                                    <option value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del
                                                        catalogo
                                                    </option>';
                                                       break;
                                                        case 'REACH':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option selected="selected" value="REACH">Copertura</option>
                                                    <option value="LOCAL_AWARENESS">Traffico</option>
                                                    <option value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'LOCAL_AWARENESS':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option selected="selected" value="LOCAL_AWARENESS">Traffico</option>
                                                    <option value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'APP_INSTALLS':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option selected="selected" value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'VIDEO_VIEWS':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option  value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option selected="selected" value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'LEAD_GENERATION':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option  value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option selected="selected" value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'POST_ENGAGEMENT':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option  value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option selected="selected" value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'PAGE_LIKES':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option  value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option  value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option selected="selected" value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'EVENT_RESPONSES'  :
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option  value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option  value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option  selected="selected" value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'MESSAGES':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option  value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option  value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option   value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option selected="selected" value="MESSAGES">Messaggi</option>
                                                    <option value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'CONVERSIONS':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option  value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option  value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option   value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option  value="MESSAGES">Messaggi</option>
                                                    <option selected="selected" value="CONVERSIONS">Conversioni</option>
                                                    <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;
                                                        case 'PRODUCT_CATALOG_SALES':
                                                            echo '<option value="BRAND_AWARENESS">Notorietà del Brand</option>
                                                    <option  value="REACH">Copertura</option>
                                                    <option  value="LOCAL_AWARENESS">Traffico</option>
                                                    <option  value="APP_INSTALLS">installazioni dell\'App</option>
                                                    <option  value="VIDEO_VIEWS">Visualizzazioni del Video</option>
                                                    <option  value="LEAD_GENERATION">Generazione di Contatti</option>
                                                    <option  value="POST_ENGAGEMENT">interazione con i post</option>
                                                    <option value="PAGE_LIKES">Mi Piace sulla Pagina</option>
                                                    <option   value="EVENT_RESPONSES">Risposte a un evento</option>
                                                    <option  value="MESSAGES">Messaggi</option>
                                                    <option  value="CONVERSIONS">Conversioni</option>
                                                    <option selected="selected" value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>';
                                                            break;

                                                    }?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="lifetime_budget">Importo Budget Totale</label>
                                                <input id="lifetime_budget" class="form-control"
                                                       placeholder="Inserisci il Budget" name="lifetime_budget" value="<?php echo $editorialPlanDetail->lifetime_budget;?>"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="titleEvent">Titolo Azione Evento</label>
                                            <input id="titleEvent" class="form-control"
                                                   placeholder="Inserisci il titolo" name="titleEvent"
                                                   value="<?php echo $editorialPlanDetail->title ?>"
                                                   required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <?php if ($editorialPlanDetail->isEventVisible == 1) {
                                                $ischecked = ' checked="true"';
                                            } else {
                                                $ischecked = '';
                                            } ?>
                                            <label for="isEventVisible">Visibile</label>
                                            <input type="checkbox" <?php echo $ischecked; ?> id="isEventVisible"
                                                   class="form-control"
                                                   placeholder="Visible" name="isEventVisible"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="description">Descrizione Evento</label>
                                            <input id="description" class="form-control"
                                                   placeholder="Inserisci la descrizione " name="description"
                                                   value="<?php echo $editorialPlanDetail->description ?> "/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisibleDescription">Visibile</label>
                                            <?php if ($editorialPlanDetail->isVisibleDescription == 1) {
                                                $ischecked = ' checked="true"';
                                            } else {
                                                $ischecked = '';
                                            } ?>
                                            <input type="checkbox" id="isVisibleDescription" class="form-control"
                                                   placeholder="Visible" <?php echo $ischecked; ?>
                                                   name="isVisibleDescription">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <form id="dropzoneModal" class="dropzone" enctype="multipart/form-data"
                                              id="photoUrl" name="photoUrl" action="POST">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="file">Immagine
                                                    Evento <?php echo '<img  width="50" src="' . $editorialPlanDetail->photoUrl . '"/>' ?></label>
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisiblePhotoUrl">Visibile</label>
                                            <?php if ($editorialPlanDetail->isVisiblePhotoUrl == 1) {
                                                $ischecked = ' checked="true"';
                                            } else {
                                                $ischecked = '';
                                            } ?>
                                            <input type="checkbox" id="isVisiblePhotoUrl" class="form-control"
                                                   placeholder="Visible" <?php echo $ischecked; ?>
                                                   name="isVisiblePhotoUrl"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="bodyEvent">Testo Evento</label>
                                                <textarea id="bodyEvent" cols="80" rows="10" name="bodyEvent"
                                                          placeholder="Inserisci il testo"><?php echo $editorialPlanDetail->bodyEvent ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="linkDestination">Link Destinazione</label>
                                                <input id="linkDestination" class="form-control"
                                                       placeholder="Inserisci la Destinazione " name="linkDestination"
                                                       value="<?php echo $editorialPlanDetail->linkDestination; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <?php if ($editorialPlanDetail->isVisibleBodyEvent == 1) {
                                                    $ischecked = ' checked="true"';
                                                } else {
                                                    $ischecked = '';
                                                } ?>
                                                <label for="isVisibleBodyEvent">Visibile</label>
                                                <input type="checkbox" id="isVisibleBodyEvent" class="form-control"
                                                       placeholder="Visible" <?php echo $ischecked; ?>
                                                       name="isVisibleBodyEvent"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="note">Note Evento</label>
                                                <textarea id="note" cols="80" rows="10" name="note"
                                                          placeholder="Inserisci le note"><?php echo $editorialPlanDetail->note ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <?php if ($editorialPlanDetail->isVisibleNote == 1) {
                                                    $ischecked = ' checked="true"';
                                                } else {
                                                    $ischecked = '';
                                                } ?>
                                                <label for="isVisibleNote">Visibile</label>
                                                <input type="checkbox" id="isVisibleNote" class="form-control"
                                                       placeholder="Visible" <?php echo $ischecked ?>
                                                       name="isVisibleNote"/>
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
                                            <?php switch ($editorialPlanDetail->status) {
                                                case 'Draft':
                                                    $option = '<option selected="selected" value="Draft">Bozza</option>' .
                                                        '<option value="Approved">Approvata</option>' .
                                                        '<option value="Rejected">Non Approvata</option>' .
                                                        '<option value="Published">Pubblicato</option>';
                                                    break;
                                                case 'Approved':
                                                    $option = '<option  value="Draft">Bozza</option>' .
                                                        '<option  selected="selected" value="Approved">Approvata</option>' .
                                                        '<option value="Rejected">Non Approvata</option>' .
                                                        '<option value="Published">Pubblicato</option>';
                                                    break;
                                                case 'Rejected':
                                                    $option = '<option  value="Draft">Bozza</option>' .
                                                        '<option value="Approved">Approvata</option>' .
                                                        '<option  selected="selected" value="Rejected">Non Approvata</option>' .
                                                        '<option value="Published">Pubblicato</option>';
                                                    break;
                                                case 'Published':
                                                    $option = '<option  value="Draft">Bozza</option>' .
                                                        '<option value="Approved">Approvata</option>' .
                                                        '<option value="Rejected">Non Approvata</option>' .
                                                        '<option selected="selected" value="Published">Pubblicato</option>';
                                                    break;
                                            } ?>
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="status">Seleziona lo Stato</label>
                                                <select id="status" name="status" required="required"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona lo stato"
                                                        data-init-plugin="selectize">
                                                    <?php echo $option ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="startEventDate">Data Inizio Evento</label>
                                                <input type="datetime-local" id="startEventDate" class="form-control"
                                                       placeholder="Inserisci la Data di Inizio del Dettaglio"
                                                       name="startEventDate"
                                                       value="<?php echo (new \DateTime($editorialPlanDetail->startEventDate))->format('Y-m-d\TH:i:s'); ?>"
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="endEventDate">Data Fine Evento </label>
                                                <input type="datetime-local" id="endEventDate" class="form-control"
                                                       placeholder="Inserisci la Data della Fine del Dettaglio "
                                                       name="endEventDate"
                                                       value="<?php echo (new \DateTime($editorialPlanDetail->endEventDate))->format('Y-m-d\TH:i:s'); ?>"
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