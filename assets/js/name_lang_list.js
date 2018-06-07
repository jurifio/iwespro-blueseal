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
    var decodedUrl = $.decodeGetStringFromUrl(url);
    var marksValue = decodedUrl['marks'];

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
                var newUrl = $.addGetParam(url, 'marks', $('[name="exclamation"]:checked').val());
                dataTable.ajax.url(newUrl);
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

$(document).on('bs.filterByTranslation', function(e){
    e.preventDefault();
    var dataTable = $('.dataTable').DataTable();
    var url = dataTable.ajax.url();
    var decodedUrl = $.decodeGetStringFromUrl(url);
    var marksValue = decodedUrl['translated'];

    modal = new $.bsModal(
        'Filtra Traduzioni',
        {
            body: '<div class="filterMarks"><p>' +
            '<input type="radio" name="isTranslated" value="tutto" /> Tutto' +
            '</p>' +
            '<p>' +
            '<input type="radio" name="isTranslated" value="senza" /> Senza traduzioni' +
            '</p>' +
            '<p>' +
            '<input type="radio" name="isTranslated" value="con" /> Solo nomi tradotti' +
            '</p>' +
            '</div>',
            okButtonEvent: function() {
                var newUrl = $.addGetParam(url, 'translated', $('[name="isTranslated"]:checked').val());
                dataTable.ajax.url(newUrl);
                dataTable.ajax.reload();
                modal.hide();
            },
            okLabel: 'Filtra',
            isCancelButton: false,
        }
    );

    $('[name="isTranslated"]').each(function(){
        var val = $(this).val();
        if (val == marksValue) $(this).prop('checked', true);
    });
});


$(document).on('bs.end.work.product.name.translation', function () {

    let selectedProductNames = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    //id-variantId in array
    $.each(selectedRows, function (k, v) {
        selectedProductNames.push(v.name);
    });

    let bsModal = new $.bsModal('Conferma Traduzione Nomi', {
        body: '<p>Confermi la fine della procedura di traduzione per i nomi selezionati?</p>'
    });

    let lang = window.location.href.split('?')[0].substring(window.location.href.lastIndexOf('/') + 1);

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            names: selectedProductNames,
            lang: lang
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductNameTranslationBatchManage',
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