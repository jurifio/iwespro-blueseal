window.buttonSetup = {
    tag:"a",
    icon:"fa-id-card-o",
    permission:"worker",
    event:"bs-manage-foison-image-photo",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Carica la tua immagine del profilo",
    placement:"bottom"
};


$(document).ready(function() {
    $(this).trigger('bs.load.photo');
});


$(document).on('bs-manage-foison-image-photo', function () {


    let foisonId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

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
        url: "/blueseal/xhr/FoisonProfileImagePhotoAjaxManage",
        maxFilesize: 5,
        maxFiles: 1,
        parallelUploads: 1,
        acceptedFiles: "image/*",
        dictDefaultMessage: "Trascina qui l'immagine da impostare come profilo",
        uploadMultiple: true,
        sending: function(file, xhr, formData) {
            formData.append("foisonId", foisonId);
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
