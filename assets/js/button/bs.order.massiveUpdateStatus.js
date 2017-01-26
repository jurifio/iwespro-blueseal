window.buttonSetup = {
    tag: "a",
    icon: "fa-cogs",
    permission: "/admin/product/delete&&allShops",
    event: "bs.order.massiveUpdateStatus",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica massiva dello stato degli ordini",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.order.massiveUpdateStatus', function (e, element, button) {
    var dataTable = $('.dataTable').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 < selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un ordine"
        }).open();
        return false;
    }

    var orders = [];
    $.each(selectedRows, function (k, v) {
        "use strict";
        orders.push(v.DT_RowId);
    });

    modal = new $.bsModal('Cambia stato agli ordini',
        {
            body: 'Sto caricando gli stati d\'ordine disponibili...'
        }
    );
    $.ajax({
        url: '/blueseal/xhr/changeOrderStatus',
        method: 'get',
        dataType: 'json',
    }).done(function (res) {
        var options = '';
        for (var i in res.statuses) {
            options += '<option value="' + i + '">' + res.statuses[i] + '</option>';
        }

        var body = '<div class="form-group form-group-default selectize-enabled">' +
            '<select class="form-control" id="SelectStatus">' +
            '<option value="" disabled selected>Seleziona uno stato degli ordini</option>' +
            options +
            '</select>' +
            '</div>';

        modal.writeBody(body);
        modal.setOkEvent(function () {
            var statusId = $('#SelectStatus').val();
            if ('' !== statusId) {
                $.ajax({
                    url: '/blueseal/xhr/changeOrderStatus',
                    method: 'put',
                    data: {orders: orders, order_status: statusId}
                }).done(function () {
                    modal.writeBody('Stato aggiornato correttamente');
                    dataTable.ajax.reload(false, null);
                }).fail(function (res) {
                    modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
                    console.error(res);
                }).always(function () {
                    modal.setOkEvent(function () {
                        modal.hide();
                    });
                });
            }
        });
    }).fail(function(res){
        modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
        console.error(res);
    });
});