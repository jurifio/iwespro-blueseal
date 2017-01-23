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

    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var timeVal = now.getFullYear() + "-" + (month) + "-" + (day);

    modal = new $.bsModal('Pagamento Fatture',
        {
            body: '<form id="invoicePaymentDateForm" class="form-group form-group-default">' +
            '<label for="invoicePaymentDate">Data del pagamento:</label>' +
            '<input type="date" id="invoicePaymentDate" name="invoicePaymentDate" class="invoicePaymentDate form-control" value="' + timeVal + '"/>' +
            '</form>',
            okButtonEvent: function(){
                $.ajax({
                    url: '/blueseal/xhr/FriendOrderPayInvoices',
                    method: 'POST',
                    dataType: 'json',
                    data: {row: row, date: $('#invoicePaymentDate').val()}
                }).done(function (res) {
                    modal.writeBody(res.message);
                    dataTable.ajax.reload(null, false);
                }).fail(function (res) {
                    modal.writeBody("OOPS! C'è stato un problemino, se il problema persiste contatta un amministratore");
                    console.error(res);
                }).always(function(){
                    modal.setOkEvent(function(){
                        modal.hide();
                    });
                });
            }
        });

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