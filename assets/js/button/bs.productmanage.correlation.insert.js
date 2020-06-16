window.buttonSetup = {
    tag: "a",
    icon: "fa-object-group",
    permission: "/admin/product/edit",
    event: "bs-productmanage-correlation-insert",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Correla Prodotti ",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-productmanage-correlation-insert', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var row = [];
    var shopIdCorr=
    $.each(selectedRows, function (k, v) {
        row.push(v.DT_RowId);
    });

    let shopId = selectedRows[0].shopId;


    let bsModal = new $.bsModal('Inserisci un Tema di Correlazione fra Prodotti', {
        body: `<div class="row">
                <div class="form-group form-group-default required">
        <label>Nome Correlazione</label>
        <input type="text" id="nameCorrelation" name="nameCorrelation" value=""/>
                </div>
                </div>
                <div class="row">
                <div class="form-group form-group-default">
                                        <label for="description">Descrizione</label>
                                        <textarea class="form-control" name="description" id="description"
                                                  value=""></textarea>
                                    </div>
                </div>
                <div class="form-group form-group-default">
                                        <label for="note">Note</label>
                                        <textarea class="form-control" name="note" id="note"
                                                  value=""></textarea>
                                    </div>
                </div>
                `
    });



    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
            name: $('#nameCorrelation').val(),
            description: $('#description').val(),
            note:$('#note').val(),
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