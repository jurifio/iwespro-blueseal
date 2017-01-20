$(document).on('bs.orderline.paymentToFriend', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 != selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una singola fattura"
        }).open();
        return false;
    }

    var row = 0;
    $.each(selectedRows, function (k, v) {
        row = v.id;
    });

    modal = new $.bsModal('Pagamento Fatture',
        {
            body: '',
        });

    $.ajax({
        url: '/blueseal/xhr/FriendOrderPayInvoices',
        method: 'POST',
        dataType: 'json',
        data: {row: row}
    }).done(function (res) {
        modal.writeBody(res.message);
    }).fail(function (res) {
        modal.writeBody("OOPS! C'è stato un problemino, se il problema persiste concattata un amministratore");
        console.error(res);
    })
});

$(document).on('bs.orderline.showInvoiceRows', function () {
    $.ajax({
        url: '/blueseal/xhr/friendInvoiceGetInvoiceLines',
        method: 'GET',
        dataType: 'ajax',
        data: {row: row}
    }).done(function () {

    }).fail(function () {

    });
});