window.buttonSetup = {
    tag:"a",
    icon:"fa-id-card-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-manage-prototype-macro-category-photo",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Gestisci foto modello categorie",
    placement:"bottom"
};


$(document).ready(function() {
    $(this).trigger('bs.load.photo');
});


$(document).on('bs-manage-prototype-macro-category-photo', function () {


    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Puoi inserire un'immagine alla volta"
        }).open();
        return false;
    }

    let macroCatId = selectedRows[0].id;

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
        $.refreshDataTable();
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
        url: "/blueseal/xhr/ProductModelPrototypeMacroCategoryPhotoAjaxManage",
        maxFilesize: 5,
        maxFiles: 1,
        parallelUploads: 1,
        acceptedFiles: "image/jpeg",
        dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
        uploadMultiple: true,
        sending: function(file, xhr, formData) {
            formData.append("macroCatId", macroCatId);
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
