
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
                                                <label for="groupAdsName">Nome del Gruppo inserzioni</label>
                                                <input id="groupAdsName" class="form-control"
                                                       placeholder="Inserisci il Nome del gruppo Inserzioni" name="groupAdsName"
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
                                            <input type="hidden" id="isNewAdset" name="isNewAdset" value="1"/>
                                            </div>
                                        </div>
                                    </div>`;
       $('#divCampaign').removeClass('hide');
       $('#divCampaign').empty();
       $('#divCampaign').append(bodyForm);

   }else{

       var bodyForm=`<div class="row">
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
            <div id="divgroupAdsName">
            
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
                   <button class="btn btn-primary" id="addCampaign" onclick="updateCampaign()" type="button"><span
                       class="fa fa-save">Aggiorna Campagna</span></button>
                   <input type="hidden" id="facebookCampaignId" name="facebookCampaignId" value=""/>
                    <input type="hidden" id="isNewAdset" name="isNewAdset" value="2"/>
               </div>
           </div>
       </div>`;
   $('#divCampaign').removeClass('hide');
   $('#divCampaign').empty();
   $('#divCampaign').append(bodyForm);
       $.ajax({
           url: '/blueseal/xhr/SelectFacebookCampaignAjaxController',
           method: 'get',
           data: {
               editorialPlanId:$('#editorialPlanId').val()
           },
           dataType: 'json'
       }).done(function (res) {
           console.log(res);
           let campaignName = $('#campaignName');
           //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
           campaignName.selectize({
               valueField: 'idCampaign',
               labelField: 'nameCampaign',
               searchField: ['nameCampaign'],
               options: res,
               render: {
                   item: function (item, escape) {
                       return '<div>' +
                           '<span class="label">' + escape(item.nameCampaign) + ' | ' + escape(item.objective) + '</span> - ' +
                           '<span class="caption">' + escape(item.buying_type) + ' | ' + escape(item.effective_status) + '</span>' +
                           '</div>'
                   },
                   option: function (item, escape) {
                       return '<div>' +
                           '<span class="label">' + escape(item.nameCampaign) + ' | ' + escape(item.objective) + '</span> - ' +
                           '<span class="caption">' + escape(item.buying_type) + ' | ' + escape(item.effective_status) + '</span>' +
                           '</div>'
                   }
               }
           });
       });
       $('#campaignName').change(function () {
           var bodyGroupAdsName=`<div class="form-group form-group-default selectize-enabled">
                <label for="groupAdsName">Seleziona il Gruppo Inserzioni</label>
                    <select id="groupAdsName"
                           name="groupAdsName" class="full-width selectpicker"
                           required="required"
                           placeholder="Selezione il Gruppo inserzioni"
                           data-init-plugin="selectize">
                   </select>
               </div>`;
           $('#divgroupAdsName').empty();
           $('#divgroupAdsName').append( bodyGroupAdsName);
           $.ajax({
               url: '/blueseal/xhr/SelectFacebookAdSetAjaxController',
               method: 'get',
               data: {
                   campaignId:$('#campaignName').val(),
                   editorialPlanId: $('#editorialPlanId').val()
               },
               dataType: 'json'
           }).done(function (res) {
               console.log(res);
               let groupAdsName = $('#groupAdsName');
               //   if (typeof (select1typePaymentId[0].selectize) != 'undefined') select1typePaymentId[0].selectize.destroy();
               groupAdsName.selectize({
                   valueField: 'idAdSet',
                   labelField: 'nameAdSet',
                   searchField: ['nameAdSet'],
                   options: res,
                   render: {
                       item: function (item, escape) {
                           return '<div>' +
                               '<span class="label">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span> - ' +
                               '<span class="caption">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span>' +
                               '</div>'
                       },
                       option: function (item, escape) {
                           return '<div>' +
                               '<span class="label">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span> - ' +
                               '<span class="caption">' + escape(item.nameAdSet) + ' | ' + escape(item.status) + '</span>' +
                               '</div>'
                       }
                   }
               });
           });


       });

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
        acceptedFiles: "image/jpeg,video/*",
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
        var facebookCampaignId='';
        if($('#facebookCampaignId').length){
            facebookCampaignId=$('#facebookCampaignId').val();
        }else{
            facebookCampaignId='notExist';
        }
        var campaignName='';
        if($('#campaignName').length){
            campaignName=$('#campaignName').val();
        }else{
            campaignName='notExist';
        }
        var groupAdsName='';
        if($('#groupAdsName').length) {
            groupAdsName = $('#groupAdsName').val();
        }
        var isNewAdSet=0;
        if($('#isNewAdset').length){
            isNewAdSet=$('#isNewAdset').val();
        }
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
            isVisibleBodyEvent: isVisBody,
            facebookCampaignId:facebookCampaignId,
            campaignId:campaignName,
            groupAdsName:groupAdsName,
            isNewAdSet:isNewAdSet




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
    let bsModal = new $.bsModal('Salva Campagna', {
        body: '<div><p>Premere ok per Salvare la Campagna' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            campaignName: $('#campaignName').val(),
            buying_type: $('#buying_type').val(),
            objective: $('#objective').val(),
            typeBudget: $('#typeBudget').val(),
            lifetime_budget: $('#lifetime_budget').val(),
            editorialPlanId:$('#editorialPlanId').val()
        }
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/CreateFacebookCampaignAjaxController',
            data: data,
            dataType:'json'
        }).done(function (res) {
            bsModal.writeBody('Campagna Creata con successo');
            $('#facebookCampaignId').val(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore Nella Creazione della Campagna');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });

}
function updateCampaign(){
    let bsModal = new $.bsModal('Salva Campagna', {
        body: '<div><p>Premere ok per Salvare la Campagna' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            campaignId: $('#campaignName').val(),
            buying_type: $('#buying_type').val(),
            objective: $('#objective').val(),
            typeBudget: $('#typeBudget').val(),
            lifetime_budget: $('#lifetime_budget').val(),
            editorialPlanId:$('#editorialPlanId').val()
        }
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/CreateFacebookCampaignAjaxController',
            data: data,
            dataType:'json'
        }).done(function (res) {
            bsModal.writeBody('Campagna Aggiornata con successo');
            $('#facebookCampaignId').val(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore Nella Creazione della Campagna');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });

}




