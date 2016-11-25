$(document).on('focusout', '.nameId', function(event) {
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

$(document).on('bs.filterByMark', function(e){
    e.preventDefault();
    var dataTable = $('.dataTable').DataTable();
    var url = dataTable.ajax.url();
    var split = url.split('marks=');
    var marksValue = split[1];
    var address = split[0];

    modal = new $.bsModal(
        'Filtra i punti esclamativi',
        {
            body: '<div class="filterMarks"><p>' +
                '<input type="radio" name="exclamation" value="tutto" /> Tutto' +
            '</p>' +
            '<p>' +
                '<input type="radio" name="exclamation" value="senza" /> Senza punti esclamativi' +
            '</p>' +
            '<p>' +
                '<input type="radio" name="exclamation" value="con" /> Solo con i punti esclamativi ' +
            '</p>' +
            '</div>',
            okButtonEvent: function() {
                dataTable.ajax.url(address + 'marks=' + $('[name="exclamation"]:checked').val());
                dataTable.ajax.reload();
                modal.hide();
            },
            okLabel: 'Filtra',
            isCancelButton: false,
        }
    );

    $('[name="exclamation"]').each(function(){
        var val = $(this).val();
        if (val == marksValue) $(this).prop('checked', true);
    });
});