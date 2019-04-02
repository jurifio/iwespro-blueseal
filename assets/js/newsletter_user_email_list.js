;(function () {
    $(document).ready(function () {
        $(this).trigger('bs.load.photo');
    });
    $(document).on('bs.newsletter.user.gender', function () {

        //Prendo tutti i prodotti selezionati
        let users = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        //id-variantId in array
        $.each(selectedRows, function (k, v) {
            users.push(v.id);
        });

        let bsModal = new $.bsModal('Aggiorna il sesso dell\'utente', {
            body: `<p>Seleziona il sesso</p>
                    <select id="sex">
                    <option disabled selected value>Seleziona il sesso</option>
                    <option value="F">Donna</option>
                    <option value="M">Uomo</option>
                    </select>
                    `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                ids: users,
                sex: $('#sex').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/NewsletterUserManageAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });
    $(document).on('bs.newsletter.user.import', function () {

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
                url: "/blueseal/xhr/NewsletteruserContactCsvImportAjaxController",
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
            url: "/blueseal/xhr/NewsletteruserContactCsvImportAjaxController",
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
            window.location.reload();
        });

    });
})();