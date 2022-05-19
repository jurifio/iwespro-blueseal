window.buttonSetup = {
    tag: "a",
    icon: "fa-arrow-up",
    permission: "/admin/product/edit&&allShops",
    event: "bs-import-ean13-csv",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Importa file Ean13 CSV",
    placement: "bottom",
    toggle: "modal"
};
$(document).ready(function () {
    $(this).trigger('bs.load.photo');
});
$(document).on('bs-import-ean13-csv', function () {
    let photoUrl = null;
    let bsModal = $('#bsModal');

    let header = bsModal.find('.modal-header h4');
    let body = bsModal.find('.modal-body');
    let cancelButton = bsModal.find('.modal-footer .btn-default');
    let okButton = bsModal.find('.modal-footer .btn-success');

    bsModal.modal();

    header.html('Carica File');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
        const data = {
            photoUrl: photoUrl,
        };
        $.ajax({
            type: 'POST',
            url: "/blueseal/xhr/ProductEanUploadAjaxManage",
            data: data,
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
    cancelButton.remove();
    let bodyContent =
        '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" id="dropzoneFile" name="dropzoneFile" action="POST">' +
        '<div class="fallback">' +
        '<input name="file" type="file" multiple />' +
        '</div>' +
        '</form>';

    body.html(bodyContent);
    let dropzone = new Dropzone("#dropzoneModal", {
        url: "/blueseal/xhr/ProductEanUploadAjaxManage",
        maxFilesize: 5,
        maxFiles: 100,
        parallelUploads: 1,
        acceptedFiles : ".xls,.xlsx,.csv",
        dictDefaultMessage: "Trascina qui i file da caricare o clicca qui",
        uploadMultiple: true,
        sending: function (file, xhr, formData) {
        }
    });

    dropzone.on('addedfile', function (file) {
        okButton.attr("disabled", "disabled");
        photoUrl =  file;
    });
    dropzone.on('queuecomplete', function () {
        okButton.removeAttr("disabled");
        $(document).trigger('bs.load.photo');
    });
});
