$(document).on('focusout', $('.nameId').val(), function(event) {
    var changed =  $(event.target);
    var translate = changed.val();
    if ("" !== translate) {
        $.ajax({
            type: "PUT",
            url: changed.data('action'),
            data: {
                translated: changed.val(),
                name: changed.data('name'),
                lang: changed.data('lang')
            }
        }).fail(function (res) {
            var bsModal = $('#bsModal');
            var header = $('.modal-header h4');
            var body = $('.modal-body');
            var cancelButton = $('.modal-footer .btn-default');
            var okButton = $('.modal-footer .btn-success');

            header.html('Traduzione');
            body.html('C\'è stato un problema nel salvataggio<br />' + res);
            okButton.html('ok').off().on('click', function () {
                bsModal.modal('hide');
                okButton.off();
            });
        });
    }
});