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
            <input type="hidden" id="editorialPlanIdSelected" name="editorialPlanIdSelected"
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
            <input type="hidden" id="userId" name="userId" value="<?php echo \Monkey::app()->getUser()->getId(); ?>"/>

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
                                            <label for="socialPlanId">Seleziona il media da Associare </label>
                                            <?php if ($allShops) { ?>
                                                <select id="socialPlanId"
                                                        required="required"
                                                        name="socialPlanId"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione il media da associare"
                                                        data-init-plugin="selectize"></select>
                                            <?php } else { ?>
                                                <select id="socialPlanId"
                                                        required="required"
                                                        name="socialPlanId"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione il media da associare"
                                                        data-init-plugin="selectize" disabled></select>
                                            <?php } ?>
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
                                                <?php if (!$allShops) {
                                                    echo " disabled ";
                                                }
                                                ?> >
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
                                                   required="required"
                                                <?php if (!$allShops) {
                                                    echo " disabled";
                                                }
                                                ?> >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="endEventDate">Data Fine Evento </label>
                                            <input type="datetime-local" id="endEventDate" class="form-control"
                                                   placeholder="Inserisci la Data della Fine del Dettaglio "
                                                   name="endEventDate"
                                                   value="<?php echo (new \DateTime($editorialPlanDetail->endEventDate))->format('Y-m-d\TH:i:s'); ?>"
                                                   required="required"
                                                <?php if (!$allShops) {
                                                    echo " disabled";
                                                }
                                                ?> >

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="notifyEmail">Notificare al Cliente</label>
                                            <?php if ($allShops) { ?>
                                                <select id="notifyEmail" name="notifyEmail" required="required"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        data-init-plugin="selectize">
                                                    <option value="notNotify">Non Inviare la Notifica</option>
                                                    <option value="yesNotify">Invia la Notifica</option>
                                                </select>
                                            <?php } else { ?>
                                                <select id="notifyEmail" name="notifyEmail" required="required"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        data-init-plugin="selectize" disabled>
                                                    <option value="notNotify">Non Inviare la Notifica</option>
                                                    <option value="yesNotify">Invia la Notifica</option>
                                                </select>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="editorialPlanArgumentId">Argomento Evento</label>
                                            <select id="editorialPlanArgumentId"
                                                    name="editorialPlanArgumentId" class="full-width selectpicker"
                                                    required="required"
                                                    placeholder="Selezione argomento da utilizzare"
                                                    data-init-plugin="selectize" <?php if (!$allShops) {
                                                echo " disabled";
                                            }
                                            ?>></select>

                                        </div>
                                    </div>
                                    <?php if ($allShops) {
                                        echo '<div class="col-md-3">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isVisibleEditorialPlanArgument">Visibile</label>';
                                        if ($editorialPlanDetail->isVisibleEditorialPlanArgument == 1) {
                                            $ischecked = ' checked="true"';
                                        } else {
                                            $ischecked = '';
                                        }
                                        echo '<input type="checkbox"' . $ischecked . ' 
                                                   id="isVisibleEditorialPlanArgument"
                                                   class="form-control"
                                                   placeholder="Visible"
                                                   name="isVisibleEditorialPlanArgument" ">
                                        </div>
                                    </div>';
                                    } ?>
                                    <div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="editorialPlanId">Seleziona Piano Editoriale</label>
                                            <?php if ($allShops) { ?>
                                                <select id="editorialPlanId"
                                                        name="editorialPlanId" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione il piano editoriale da utilizzare"
                                                        data-init-plugin="selectize"></select>
                                            <?php } else { ?>
                                                <select id="editorialPlanId"
                                                        name="editorialPlanId" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione il piano editoriale da utilizzare"
                                                        data-init-plugin="selectize" disabled></select>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="hidden" id='contractSelectId' name="contractSelectId"
                                               value="<?php echo $contractId ?>"/>
                                        <input type="hidden" id='foisonSelectId' name="foisonSelectId"
                                               value="<?php echo $foisonId ?>"/>
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="foisonId">Seleziona Operatore</label>
                                            <?php if ($allShops) {
                                                echo '<select id="foisonId"
                                                        name="foisonId" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione Operatore"
                                                        data-init-plugin="selectize"></select>';
                                            } else {
                                                echo '<select id="foisonId"
                                                        name="foisonId" class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Selezione Operatore"
                                                        data-init-plugin="selectize" disabled></select>';
                                            } ?>
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
                                                        data-init-plugin="selectize" <?php if (!$allShops) {
                                                    echo " disabled";
                                                }
                                                ?>>
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
                                                        data-init-plugin="selectize" <?php if (!$allShops) {
                                                    echo " disabled";
                                                }
                                                ?>>
                                                    <?php echo '<option value="' . $campaignSelected . '">' . $nameCampaignSelected . '</option>' ?>
                                                    ;
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
                                                        data-init-plugin="selectize" <?php if (!$allShops) {
                                                    echo " disabled";
                                                }
                                                ?>>
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
                                                        data-init-plugin="selectize" <?php if (!$allShops) {
                                                    echo " disabled";
                                                }
                                                ?>>
                                                    <?php if ($editorialPlanDetail->buying_type == 'AUCTION') {
                                                        echo '<option selected="selected" value="AUCTION">Asta</option>
                                                    <option value="RESERVED">Copertura e Frequenza</option>';
                                                    } else {
                                                        echo '<option  value="AUCTION">Asta</option>
                                                    <option  selected="selected" value="RESERVED">Copertura e Frequenza</option>';
                                                    } ?>
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
                                                        data-init-plugin="selectize" <?php if (!$allShops) {
                                                    echo " disabled";
                                                }
                                                ?>>
                                                    <?php switch ($editorialPlanDetail->objective) {
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

                                                    } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="lifetime_budget">Importo Budget Totale</label>
                                                <input id="lifetime_budget" class="form-control"
                                                       placeholder="Inserisci il Budget" name="lifetime_budget"
                                                       value="<?php echo $editorialPlanDetail->lifetime_budget; ?>"
                                                       required="required" <?php if (!$allShops) {
                                                    echo " disabled";
                                                }
                                                ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="titleEvent">Titolo Azione Evento</label>
                                            <input id="titleEvent" class="form-control"
                                                   placeholder="Inserisci il titolo" name="titleEvent"
                                                   value="<?php echo $editorialPlanDetail->title ?>"
                                                   required="required">
                                        </div>
                                    </div>
                                    <?php if ($allShops) {
                                        echo '<div class="col-md-2">
                                        <div class="form-group form-group-default selectize-enabled">';
                                        if ($editorialPlanDetail->isEventVisible == 1) {
                                            $ischecked = ' checked="true"';
                                        } else {
                                            $ischecked = '';
                                        }
                                        echo '<label for="isEventVisible">Visibile</label>
                                            <input type="checkbox" ' . $ischecked . ' id="isEventVisible"
                                                   class="form-control"
                                                   placeholder="Visible" name="isEventVisible"/>
                                        </div>
                                    </div>';
                                    } ?>
                                    <div class="col-md-4">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="description">Descrizione Evento</label>
                                            <input id="description" class="form-control"
                                                   placeholder="Inserisci la descrizione " name="description"
                                                   value="<?php echo $editorialPlanDetail->description ?> "/>
                                        </div>
                                    </div>
                                    <?php if ($allShops) { ?>
                                        <div class="col-md-2">
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
                                    <?php } ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="bodyEvent">Testo Evento</label>
                                                    <textarea id="bodyEvent" cols="180" rows="10" name="bodyEvent"
                                                              placeholder="Inserisci il testo"><?php echo $editorialPlanDetail->bodyEvent ?></textarea>
                                                </div>
                                            </div>
                                            <?php if ($allShops) { ?>
                                                <div class="col-md-2">
                                                    <div class="form-group form-group-default selectize-enabled">
                                                        <?php if ($editorialPlanDetail->isVisibleBodyEvent == 1) {
                                                            $ischecked = ' checked="true"';
                                                        } else {
                                                            $ischecked = '';
                                                        } ?>
                                                        <label for="isVisibleBodyEvent">Visibile</label>
                                                        <input type="checkbox" id="isVisibleBodyEvent"
                                                               class="form-control"
                                                               placeholder="Visible" <?php echo $ischecked; ?>
                                                               name="isVisibleBodyEvent"/>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <b>Immagine
                                            Evento</b> <?php echo '<img  width="250" src="' . $editorialPlanDetail->photoUrl . '"/>' ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="note">Note Evento</label>
                                            <textarea id="note" cols="180" rows="10" name="note"
                                                      placeholder="Inserisci le note"><?php echo $editorialPlanDetail->note ?></textarea>
                                        </div>
                                    </div>
                                    <?php if ($allShops) { ?>
                                        <div class="col-md-2">
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
                                    <?php } ?>
                                </div>
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="linkDestination">Link Destinazione</label>
                                            <input id="linkDestination" class="form-control"
                                                   placeholder="Inserisci la Destinazione " name="linkDestination"
                                                   value="<?php echo $editorialPlanDetail->linkDestination; ?>"/>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($editorialPlanDetail->editorialPlanArgumentId == 8 || $editorialPlanDetail->editorialPlanArgumentId == 5 || $editorialPlanDetail->editorialPlanArgumentId == 9) {
                                    echo '<div id="divPostUploadImage" class="show">';
                                } else {
                                    echo '<div id="divPostUploadImage" class="hide">';
                                } ?>
                                <hr>
                                <div class="row">
                                    <div class="col-md-10">
                                        <form id="dropzoneModal" class="dropzone" enctype="multipart/form-data"
                                              id="photoUrl" name="photoUrl" action="POST">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="file">Creatività:Immagine</label>
                                                <div class="fallback">
                                                    <input name="file" type="file" multiple/>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <?php if ($allShops) { ?>
                                        <div class="col-md-2">
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
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if ($editorialPlanDetail->editorialPlanArgumentId == 8 || $editorialPlanDetail->editorialPlanArgumentId == 5) {
                                echo '<div id="divPostImage" class="show">';
                            } else {
                                echo '<div id="divPostImage" class="hide">';
                            } ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="postImageTitle">Creatività:Titolo Post</label>
                                        <textarea id="postImageTitle" class="form-control"
                                                  placeholder="Inserisci il titolo per l'immagine "
                                                  name="postImageTitle"
                                        ><?php echo $editorialPlanDetail->postImageTitle; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="postImageDescription">Creatività:Descrizione Post </label>
                                        <textarea id="postImageDescription" class="form-control"
                                                  placeholder="Inserisci la descrizione per l'immagine 1"
                                                  name="postImageDescription"
                                        ><?php echo $editorialPlanDetail->postImageDescription; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="postImageUrl">Creatività:link Immagine</label>
                                        <textarea id="postImageUrl" class="form-control"
                                                  placeholder="Inserisci il link per l'immagine 1"
                                                  name="postImageUrl"
                                        ><?php echo $editorialPlanDetail->postImageUrl; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($editorialPlanDetail->editorialPlanArgumentId == 9) {
                            echo '<div id="divPostCarousel" class="show">';
                        } else {
                            echo '<div id="divPostCarousel" class="hide">';
                        } ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle1">Creativita:Titolo Immagine1</label>
                                    <textarea id="imageTitle1" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 1"
                                              name="imageTitle1"
                                    ><?php echo $editorialPlanDetail->imageTitle1; ?></textarea>
                                    <input type="hidden" id="imageHash1" name="imageHash1"
                                           value="<?php echo $editorialPlanDetail->imageHash1; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage1">Creatività:Descrizione Post Immagine1</label>
                                    <textarea id="descriptionImage1" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 1"
                                              name="descriptionImage1"
                                    ><?php echo $editorialPlanDetail->descriptionImage1; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl1">Creatività:link Immagine1</label>
                                    <textarea id="imageUrl1" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 1"
                                              name="imageUrl1"
                                    ><?php echo $editorialPlanDetail->imageUrl1; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle2">Creatività:Titolo Post Immagine2</label>
                                    <textarea id="imageTitle2" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 1"
                                              name="imageTitle2"
                                    ><?php echo $editorialPlanDetail->imageTitle2; ?></textarea>
                                    <input type="hidden" id="imageHash2" name="imageHash2"
                                           value="<?php echo $editorialPlanDetail->imageHash2; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage2">Creatività:Descrizione Immagine2</label>
                                    <textarea id="descriptionImage2" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 2"
                                              name="descriptionImage1"
                                    ><?php echo $editorialPlanDetail->descriptionImage2; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl2">Creatività:link Immagine2</label>
                                    <textarea id="imageUrl2" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 2"
                                              name="imageUrl2"
                                    ><?php echo $editorialPlanDetail->imageUrl2; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle3">Creatività:Titolo Post Immagine3</label>
                                    <textarea id="imageTitle3" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 3"
                                              name="imageTitle3"
                                    ><?php echo $editorialPlanDetail->imageTitle3; ?></textarea>
                                    <input type="hidden" id="imageHash3" name="imageHash3"
                                           value="<?php echo $editorialPlanDetail->imageHash3; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage3">Creatività:Descrizione Immagine3</label>
                                    <textarea id="descriptionImage3" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 3"
                                              name="descriptionImage3"
                                    ><?php echo $editorialPlanDetail->descriptionImage3; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl3">Creatività:link Immagine3</label>
                                    <textarea id="imageUrl3" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 3"
                                              name="imageUrl3"
                                    ><?php echo $editorialPlanDetail->imageUrl3; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle4">Creatività:Titolo Post Immagine4</label>
                                    <textarea id="imageTitle4" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 4"
                                              name="imageTitle4"
                                    ><?php echo $editorialPlanDetail->imageTitle4; ?></textarea>
                                    <input type="hidden" id="imageHash4" name="imageHash4"
                                           value="<?php echo $editorialPlanDetail->imageHash4; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage4">Creatività:Descrizione Immagine4</label>
                                    <textarea id="descriptionImage4" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 4"
                                              name="descriptionImage4"
                                    ><?php echo $editorialPlanDetail->descriptionImage4; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl4">Creatività:link Immagine4</label>
                                    <textarea id="imageUrl4" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 4"
                                              name="imageUrl4"
                                    ><?php echo $editorialPlanDetail->imageUrl4; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle5">Creatività:Titolo Post Immagine5</label>
                                    <textarea id="imageTitle5" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 5"
                                              name="imageTitle5"
                                    ><?php echo $editorialPlanDetail->imageTitle5; ?></textarea>
                                    <input type="hidden" id="imageHash5" name="imageHash5"
                                           value="<?php echo $editorialPlanDetail->imageHash5; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage5">Creatività:Descrizione Immagine5</label>
                                    <textarea id="descriptionImage5" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 5"
                                              name="descriptionImage5"
                                    ><?php echo $editorialPlanDetail->descriptionImage5; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl5">Creatività:link Immagine5</label>
                                    <textarea id="imageUrl5" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 5"
                                              name="imageUrl5"><?php echo $editorialPlanDetail->imageUrl5; ?></textarea>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle6">Creatività:Titolo Post Immagine6</label>
                                    <textarea id="imageTitle6" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 6"
                                              name="imageTitle6"
                                    ><?php echo $editorialPlanDetail->imageTitle6; ?></textarea>
                                    <input type="hidden" id="imageHash6" name="imageHash6"
                                           value="<?php echo $editorialPlanDetail->imageHash6; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage6">Creatività:Descrizione Immagine6</label>
                                    <textarea id="descriptionImage6" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 6"
                                              name="descriptionImage6"
                                              required="required"><?php echo $editorialPlanDetail->descriptionImage6; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl6">Creatività:link Immagine6</label>
                                    <textarea id="imageUrl6" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 6"
                                              name="imageUrl6"
                                    ><?php echo $editorialPlanDetail->imageUrl6; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle7">Creatività:Titolo Post Immagine7</label>
                                    <textarea id="imageTitle7" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 7"
                                              name="imageTitle7"
                                    ><?php echo $editorialPlanDetail->imageTitle7; ?></textarea>
                                    <input type="hidden" id="imageHash7" name="imageHash7"
                                           value="<?php echo $editorialPlanDetail->imageHash7; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage7">Creatività:Descrizione Immagine7</label>
                                    <textarea id="descriptionImage7" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 7"
                                              name="descriptionImage7"
                                              required="required"><?php echo $editorialPlanDetail->descriptionImage7; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl7">Creatività:link Immagine7</label>
                                    <textarea id="imageUrl7" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 7"
                                              name="imageUrl7"
                                    ><?php echo $editorialPlanDetail->imageUrl7; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle8">Creatività:Titolo Post Immagine6</label>
                                    <textarea id="imageTitle8" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 8"
                                              name="imageTitle8"
                                    ><?php echo $editorialPlanDetail->imageTitle8; ?></textarea>
                                    <input type="hidden" id="imageHash8" name="imageHash8"
                                           value="<?php echo $editorialPlanDetail->imageHash8; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage8">Creatività:Descrizione Immagine8</label>
                                    <textarea id="descriptionImage8" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 8"
                                              name="descriptionImage8"
                                              required="required"><?php echo $editorialPlanDetail->descriptionImage8; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl8">Creatività:link Immagine8</label>
                                    <textarea id="imageUrl8" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 8"
                                              name="imageUrl8"
                                    ><?php echo $editorialPlanDetail->imageUrl8; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle9">Creatività:Titolo Post Immagine9</label>
                                    <textarea id="imageTitle9" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 9"
                                              name="imageTitle9"
                                    ><?php echo $editorialPlanDetail->imageTitle9; ?></textarea>
                                    <input type="hidden" id="imageHash9" name="imageHash9"
                                           value="<?php echo $editorialPlanDetail->imageHash9; ?>"/>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage9">Creatività:Descrizione Immagine9</label>
                                    <textarea id="descriptionImage9" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 9"
                                              name="descriptionImage9"
                                              required="required"><?php echo $editorialPlanDetail->imageUrl9; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl9">Creatività:link Immagine6</label>
                                    <textarea id="imageUrl9" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 9"
                                              name="imageUrl9"
                                    ><?php echo $editorialPlanDetail->imageUrl9; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageTitle10">Creatività:Titolo Post Immagine10</label>
                                    <textarea id="imageTitle10" class="form-control"
                                              placeholder="Inserisci il titolo per l'immagine 10"
                                              name="imageTitle10"
                                    ><?php echo $editorialPlanDetail->imageTitle10; ?></textarea>
                                    <input type="hidden" id="imageHash10" name="imageHash10"
                                           value="<?php echo $editorialPlanDetail->imageHash10; ?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="descriptionImage10">Creatività:Descrizione Immagine10</label>
                                    <textarea id="descriptionImage10" class="form-control"
                                              placeholder="Inserisci la descrizione per l'immagine 10"
                                              name="descriptionImage10"
                                              required="required"><?php echo $editorialPlanDetail->descriptionImage10; ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="imageUrl10">Creatività:link Immagine10</label>
                                    <textarea id="imageUrl10" class="form-control"
                                              placeholder="Inserisci il link per l'immagine 10"
                                              name="imageUrl10"
                                    ><?php echo $editorialPlanDetail->imageUrl10; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($editorialPlanDetail->editorialPlanArgumentId == 10) {
                        echo '<div id="postVideo" class="show">';
                    } else {
                        echo '<div id="postVideo" class="hide">';
                    } ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group form-group-default selectize-enabled">
                                <label for="postVideoTitle">Creatività:Titolo Video</label>
                                <textarea id="postVideoTitle" class="form-control"
                                          placeholder="Inserisci il titolo per l'immagine "
                                          name="postVideoTitle"
                                ><?php echo $editorialPlanDetail->postVideoTitle; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-group-default selectize-enabled">
                                <label for="postDescriptionVideo">Creatività:Descrizione Video</label>
                                <textarea id="postDescriptionVideo" class="form-control"
                                          placeholder="Inserisci la descrizione per l'immagine 1"
                                          name="postDescriptionVideo"
                                ><?php echo $editorialPlanDetail->postDescriptionVideo; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-group-default selectize-enabled">
                                <label for="video1">Creatività:link Video</label>
                                <textarea id="video1" class="form-control"
                                          placeholder="Inserisci il link per il video"
                                          name="video"><?php echo $editorialPlanDetail->video1; ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group form-group-default selectize-enabled">
                                <label for="postVideoCallToAction">Creatività:Seleziona la Call To Action</label>
                                <select id="postVideoCallToAction"
                                        name="postVideoCallToAction" class="full-width selectpicker"
                                        required="required"
                                        placeholder="Selezione il piano editoriale da utilizzare"
                                        data-init-plugin="selectize">
                                    <?php switch ($editorialPlanDetail->postVideoCallToAction) {
                                        case 'OPEN_LINK':
                                            echo ' <option selected="selected" value="OPEN_LINK">APRI LINK</option>
                                                    <option value="LIKE_PAGE">MI PIACE SU PAGINA</option>
                                                    <option value="SHOP_NOW">SHOP NOW</option>
                                                    <option value="CALL">CALL_ME</option>
                                                    <option value="APPLY_NOW">APPLY NOW</option>';
                                            break;
                                        case 'LIKE_PAGE':
                                            echo ' <option  value="OPEN_LINK">APRI LINK</option>
                                                    <option selected="selected" value="LIKE_PAGE">MI PIACE SU PAGINA</option>
                                                    <option value="SHOP_NOW">SHOP NOW</option>
                                                    <option value="CALL">CALL_ME</option>
                                                    <option value="APPLY_NOW">APPLY NOW</option>';
                                            break;
                                        case 'SHOP_NOW':
                                            echo ' <option  value="OPEN_LINK">APRI LINK</option>
                                                    <option  value="LIKE_PAGE">MI PIACE SU PAGINA</option>
                                                    <option selected="selected" value="SHOP_NOW">SHOP NOW</option>
                                                    <option value="CALL">CALL_ME</option>
                                                    <option value="APPLY_NOW">APPLY NOW</option>';
                                            break;
                                        case 'CALL':
                                            echo ' <option  value="OPEN_LINK">APRI LINK</option>
                                                    <option  value="LIKE_PAGE">MI PIACE SU PAGINA</option>
                                                    <option  value="SHOP_NOW">SHOP NOW</option>
                                                    <option selected="selected" value="CALL">CALL_ME</option>
                                                    <option value="APPLY_NOW">APPLY NOW</option>';
                                            break;
                                        case 'APPLY_NOW':
                                            echo ' <option  value="OPEN_LINK">APRI LINK</option>
                                                    <option  value="LIKE_PAGE">MI PIACE SU PAGINA</option>
                                                    <option  value="SHOP_NOW">SHOP NOW</option>
                                                    <option  value="CALL">CALL_ME</option>
                                                    <option selected="selected" value="APPLY_NOW">APPLY NOW</option>';
                                            break;
                                    }
                                    ?>
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