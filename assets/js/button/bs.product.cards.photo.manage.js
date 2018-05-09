window.buttonSetup = {
    tag:"a",
    icon:"fa-id-card-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-manage-cards-photo",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Gestisci foto scheda prodotto",
    placement:"bottom"
};


$(document).ready(function() {
    $(this).trigger('bs.load.photo');
});


$(document).on('bs-manage-cards-photo', function () {

    let bsModal = $('#bsModal');

    let header = bsModal.find('.modal-header h4');
    let body = bsModal.find('.modal-body');
    let cancelButton = bsModal.find('.modal-footer .btn-default');
    let okButton = bsModal.find('.modal-footer .btn-success');

    bsModal.modal();

    header.html('Carica Foto');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();
    let bodyContent =
        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">'+
        '<div class="fallback">'+
        '<input name="file" type="file" multiple />' +
        '</div>' +
        '</form>';

    body.html(bodyContent);
    let dropzone = new Dropzone("#dropzoneModal",{
        url: "/blueseal/xhr/ProductCardsPhotoAjaxManage",
        maxFilesize: 4,
        maxFiles: 10,
        parallelUploads: 10,
        acceptedFiles: "image/jpeg",
        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
        uploadMultiple: true,
        sending: function(file, xhr, formData) {
            formData.append("id", $.QueryString["id"]);
            formData.append("productletiantId", $.QueryString["productletiantId"]);
        }
    });

    dropzone.on('addedfile',function(){
        okButton.attr("disabled", "disabled");
    });
    dropzone.on('queuecomplete',function(){
        okButton.removeAttr("disabled");
        $(document).trigger('bs.load.photo');
    });
    
});
