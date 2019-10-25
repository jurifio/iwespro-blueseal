window.buttonSetup = {
    tag: "a",
    icon: "fa-american-sign-language-interpreting",
    permission: "/admin/product/list",
    event: "bs-orderline-change-status",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica Lo Stato delle Righe Ordini",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-orderline-change-status', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if ( selectedRows.length < 1 || selectedRows.length > 1 ) {
        new Alert({
            type: "warning",
            message: "Devi selezionare solo un  prodotto alla volta"
        }).open();
        return false;
    }

    var row = [];
    $.each(selectedRows, function (k, v) {
        row.push(v.line_id);
    });


    var modal = new $.bsModal('Cambia Stato', {
        body:  '<div class="form-group form-group-default required">' +
            '<label for="statusLine">Cambia Lo Stato</label>' +
            '<select id="statusLine" name="statusLine">' +
            '<option disabled selected value>Seleziona ----------</option>' +
            '</select>' +
            '</div>'
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'OrderLineStatus'
        },
        dataType: 'json'
    }).done(function (res) {
        let select = $('#statusLine');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'code',
            labelField: 'title',
            options: res,
        });
    });





    modal.setOkEvent(function () {
        $.ajax({
            url: '/blueseal/xhr/ChangeFromAdminLineStatus',
            method: 'POST',
            data: {
                rows: row,
                statusLine: $('#statusLine').val()

            }
        }).done(function (res) {
           modal.writeBody(res);
        }).fail(function (res) {
            modal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });

    });

});