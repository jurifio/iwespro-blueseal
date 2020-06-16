window.buttonSetup = {
    tag: "a",
    icon: "fa-plus-circle",
    permission: "/admin/product/edit",
    event: "bs-product-correlation.insert",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi Correlazione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-correlation.insert', function () {
    var urldef='';

    let bsModal = new $.bsModal('Inserisci un Tema di Correlazione fra Prodotti', {
        body: `<div class="row">
                <div class="form-group form-group-default required">
        <label>Nome Correlazione</label>
        <input type="text" id="nameCorrelation" name="nameCorrelation" value=""/>
                </div>
                </div>
                <div class="row">
               <div class="form-group form-group-default selectize-enabled">
                                        <label for="code">seleziona il Tipo di Correlazione</label>
                                        <select id="code" name="code"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <option  value="APP">Potrebbe Piacerti Anche</option>
                                            <option  value="LOOK">look</option>
                                            <option  value="COLOUR">Colore</option>
                                        </select>
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
                                        <textarea class="form-control" name="note" id="note"
                                                  value=""></textarea>
                                    </div>
                </div>
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="seo">Seo</label>
                                        <textarea class="form-control" name="seo" id="seo"
                                                  value=""></textarea>
                                    </div>
                </div>
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

                        '</form>';
                `
    });
    let dropzone = new Dropzone("#dropzoneModal",{
        url: '/blueseal/xhr/EditorialPlanDetailImageUploadAjaxManage',

        maxFilesize: 5,
        maxFiles: 100,
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
         urldef=image;
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
            name: $('#nameCorrelation').val(),
            description: $('#description').val(),
            note:$('#note').val(),
            seo:$('#seo').val(),
            code:$('#code').val(),
            image:urldef
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductCorrelationAjaxController',
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