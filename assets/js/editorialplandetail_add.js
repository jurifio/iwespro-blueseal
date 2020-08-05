
(function ($) {

    Pace.ignore(function () {
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'EditorialPlan'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#editorialPlanId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'EditorialPlanArgument'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#editorialPlanArgumentId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'titleArgument',
                searchField: 'titleArgument',
                options: res2,
            });
        });
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'EditorialPlanSocial'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#socialPlanId');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });
        });



    });
$('#editorialPlanArgumentId').change(function(){
   if($('#editorialPlanArgumentId').val()>1 && $('#editorialPlanArgumentId').val()<10){
        $('#divSelecterCampaign').removeClass('hide');
       $('#divSelecterCampaign').addClass('show');
   }else{
       $('#divSelecterCampaign').removeClass('show');
       $('#divSelecterCampaign').addClass('hide');
   }
});
$('#selecterCampaign').change(function () {
   var selecterTypeOperation = $(this).val();
   if(selecterTypeOperation =='0'){
       var bodyForm=`               <div class="row">
                                         <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="campaignName">Nome Della Campagna</label>
                                                <input id="campaignName" class="form-control"
                                                       placeholder="Inserisci il nome Campagna" name="campaignName"
                                                       required="required">
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
                                                        <option value="AUCTION">Asta</option>
                                                        <option value="RESERVED">Copertura e Frequenza</option>
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
                                                       <option value="BRAND_AWARENESS">Notorietà del Brand</option>
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
                                                       <option value="PRODUCT_CATALOG_SALES">Vendita dei prodotti del catalogo</option>
                                                       <option value="STORE_VISITS">Traffico nel punto Vendita</option>
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="lifetime_budget">Importo Budget Totale</label>
                                                <input id="lifetime_budget" class="form-control"
                                                       placeholder="Inserisci il Budget" name="lifetime_budget"
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                           <div class="form-group form-group-default selectize-enabled">
                                        <button class="btn btn-primary" id="addCampaign" onclick="addCampaign()" type="button"><span
                                                class="fa fa-save">Salva Campagna</span></button>
                                            <input type="hidden" id="facebookCampaignId" name="facebookCampaignId" value=""/> 
                                            </div>
                                        </div>
                                    </div>`;
       $('#divCampaign').removeClass('hide');
       $('#divCampaign').empty();
       $('#divCampaign').append(bodyForm);

   }else{

   }

});



})(jQuery);
var photoUrl=[];
Dropzone.autoDiscover = false;
$(document).ready(function () {

    let dropzone = new Dropzone("#dropzoneModal", {
        url: '/blueseal/xhr/EditorialPlanDetailImageUploadAjaxManage',

        maxFilesize: 5,
        maxFiles: 100,
        parallelUploads: 10,
        acceptedFiles: "image/jpeg",
        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
        uploadMultiple: true,
        sending: function (file, xhr, formData) {

        }
    });

    dropzone.on('addedfile', function (file) {
        let urlimage = "https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/";
        let filename = file.name;
        let image = urlimage + filename;
        photoUrl.push(image);
    });
    dropzone.on('queuecomplete', function () {
        $(document).trigger('bs.load.photo');
    });
});


$(document).on('bs.post.save', function () {
    let bsModal = new $.bsModal('Salva Post', {
        body: '<div><p>Premere ok per Salvare il Piano Editoriale' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        var isEvVisible = ($('#isEventVisible').is(":checked") ? "1" : "0");
        var isVisEdPlanArg = ($('#isVisibleEditorialPlanArgument').is(":checked") ? "1" : "0");
        var isVisDesc = ($('#isVisibleDescription').is(":checked") ? "1" : "0");
        var isVisNote = ($('#isVisibleNote').is(":checked") ? "1" : "0");
        var isVisBody = ($('#isVisibleBodyEvent').is(":checked") ? "1" : "0");
        var isVisPhoto = ($('#isVisiblePhotoUrl').is(":checked") ? "1" : "0");
        start = $('#startEventDate').val();
        end = $('#endEventDate').val();
        const data = {
            title: $('#titleEvent').val(),
            start: start,
            end: end,
            argument: $('#editorialPlanArgumentId').val(),
            description: $('#description').val(),
            linkDestination: $('#linkDestination').val(),
            note: $('#note').val(),
            isVisibleNote: isVisNote,
            photoUrl: photoUrl,
            status: $('#status').val(),
            socialId: $('#socialPlanId').val(),
            editorialPlanId: $('#editorialPlanId').val(),
            notifyEmail: $('#notifyEmail').val(),
            isEventVisible: isEvVisible,
            isVisibleEditorialPlanArgument: isVisEdPlanArg,
            isVisibleDescription: isVisDesc,
            isVisiblePhotoUrl: isVisPhoto,
            bodyEvent: $('#bodyEvent').val(),
            isVisibleBodyEvent: isVisBody


        };

        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/EditorialPlanDetailAddAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});
function addCampaign(){
    let bsModal = new $.bsModal('Salva Post', {
        body: '<div><p>Premere ok per Salvare il Piano Editoriale' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            campaignName: $('#campaignName').val(),
            buying_type: $('#buying_type').val(),
            objective: $('#objective').val(),
            typeBudget: $('#typeBudget').val(),
            daily_budget: $('#daily_budget').val(),
            lifetime_budget: $('#lifetime_budget').val(),
            editorialPlanId:$('#editorialPlanId').val()
        }
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/CreateFacebookCampaignAjaxController',
            data: data,
            dataType:'json'
        }).done(function (res) {
            bsModal.writeBody(res);
            $('#facebookCampaignId').val(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });

}




