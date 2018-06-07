$(document).on('bs.translate.name', function() {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    var result = {
        status: "ko",
        bodyMessage: "Errore di caricamento, controlla la rete",
        okButtonLabel: "Ok",
        cancelButtonLabel: null
    };

    header.html('Traduci in:');
    $.ajax({
        url: "/blueseal/xhr/NameTranslateManager",
        type: "GET"
    }).done(function (response) {
        result = JSON.parse(response);
        body.html(result.bodyMessage);
        $(bsModal).find('table').addClass('table');

        if (result.cancelButtonLabel == null) {
            cancelButton.hide();
        } else {
            cancelButton.html(result.cancelButtonLabel);
        }

        bsModal.modal();
        if (result.status == 'ok') {
            okButton.html(result.okButtonLabel).off().on('click', function (e) {
                var selected = $('input[name="langId"]:checked').val();
                window.location.replace('/blueseal/traduzioni/nomi/lingua/'+selected);
            });

        }

    });
});


$(document).on('bs.name.to.batch', function() {

    let pNames = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    $.each(selectedRows, function (k, v) {
        pNames.push(v.name);
    });

    if(selectedRows.length < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un nome"
        }).open();
        return false;
    }

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Lang',
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#lang');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            options: res
        });
    });

    let bsModal = new $.bsModal('Assegna nomi prodotto/lingua al lotto', {
        body: `
                <p>Inserisci il numero del lotto</p>
                <input type="number" min="1" id="productBatchId">
                <p>Seleziona la lingua</p>
                <select id="lang" name="lang"></select>
                `
    });


    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            productBatchId: $('#productBatchId').val(),
            langId: $('#lang').val(),
            pNames: pNames
        };

        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductBatchNameTranslateManage',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable();
            });
            bsModal.showOkBtn();
        });
    });
});