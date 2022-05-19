window.buttonSetup = {
    tag: "a",
    icon: "fa-plus-circle",
    permission: "/admin/product/edit",
    event: "bs-product-correlation.insert",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi Look",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-correlation.insert', function () {
    var urldef=[];


    let bsModal = new $.bsModal('Inserisci un Look', {
        body: `<div class="row">
                <div class="form-group form-group-default required">
                <label for="nameLook">Nome look</label>
                <input type="text" id="nameLook" name="nameLook" value=""/>
                </div>
                </div>
                  <div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="shopId">seleziona lo Shop Di Destinazione</label>
                                        <select id="shopId" name="shopId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize"></select>
                                                
                                    </div>
                </div>
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="description">Descrizione</label>
                                        <textarea class="form-control" name="description" id="description"
                                                  value=""></textarea>
                                    </div>
                </div>
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="note">Note</label>
                                        <textarea class="form-control" name="ntoe" id="note"
                                                  value=""></textarea>
                                    </div>
                </div>
                <div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="discountActive">Sconto Attivo</label>
                                        <select id="discountActive" name="discountActive"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option  value="1">Sì</option>
                                            <option  value="0">no</option>
                                        </select>
                                    </div>
                </div>
                <div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeDiscount">tipo di Sconto</label>
                                        <select id="typeDiscount" name="typeDiscount"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option  value="1">Percentuale</option>
                                            <option  value="2">Fisso</option>
                                        </select>
                                    </div>
                </div>
               
                <div class="row">
               <div class="form-group form-group-default">
                                        <label for="amount">Valore</label>
                                        <input type="text" id="amount" name="amount" step="0.01" value=""/>
                                    </div>
                </div>
                <div class="row">  
                 <div class="form-group form-group-default">
                        <form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" id="photoUrl" name="photoUrl" action="POST">
                        <div class="form-group form-group-default selectize-enabled\\">
                        <label for="file">Immagine</label> 
                        <div class="fallback">
                        <label for="file">immagine</label>
                        <input name="file" type="file" multiple />
                        </div>
                        </div>
                        </div>
                     
                        </form>
                        </div>
                `
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop',
            condition: {hasEcommerce: 1}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#shopId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    let dropzone = new Dropzone("#dropzoneModal",{
        url: '/blueseal/xhr/EditorialPlanDetailImageUploadAjaxManage',

        maxFilesize: 5,
        maxFiles: 4,
        parallelUploads: 10,
        acceptedFiles: "image/jpeg",
        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
        uploadMultiple: true,
        sending: function(file, xhr, formData) {

        }
    });

    dropzone.on('addedfile',function(file){
        let urlimage="https://iwes-editorial.s3-eu-west-1.amazonaws.com/plandetail-images/";
        let filename=file.name;
        let image =urlimage+filename;

        urldef.push(image);
    });
    dropzone.on('queuecomplete',function(){
        okButton.removeAttr("disabled");
        $(document).trigger('bs.load.photo');
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
            name: $('#nameLook').val(),
            description: $('#description').val(),
            typeDiscount:$('#typeDiscount').val(),
            discountActive:$('#discountActive').val(),
            shopId:$('#shopId').val(),
            amount:$('#amount').val(),
            note:$('#note').val(),
            image:urldef
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductLookAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Si è verificato un errore')
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $('.table').DataTable().ajax.reload();
            });
        });
    });
});