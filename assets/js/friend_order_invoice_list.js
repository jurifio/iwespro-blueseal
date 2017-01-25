$(document).on('bs.orderline.paymentToFriend', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno una fattura"
        }).open();
        return false;
    }

    var row = [];
    var i = 0;
    $.each(selectedRows, function (k, v) {
        row[i] = v.id;
        i++;
    });



    modal = new $.bsModal('Pagamento Fatture',
        {}
    );

    $.ajax({
        url: '/blueseal/xhr/FriendOrderPayInvoices',
        method: 'GET',
        dataType: 'json',
        data: {row: row}
    }).done(function(res){
        if (res.error) {
            modal.writeBody(res.message);
            modal.setOkEvent(function(){
                modal.hide();
            });
        } else {
            var now = new Date();
            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);
            var timeVal = now.getFullYear() + "-" + (month) + "-" + (day);

            var readonly = (1 < row.length) ? 'readonly' : '';

            modal.writeBody('<form id="invoicePaymentDateForm" class="form-group form-group-default">' +
                '<label for="billAmount">Ammontare:</label>' +
                '<input type="text" id="billAmount" name="billAmount" class="billAmount form-control" value="' + res + '" ' + readonly + ' />' +
                '<label for="invoicePaymentDate">Data del pagamento:</label>' +
                '<input type="date" id="invoicePaymentDate" name="invoicePaymentDate" class="invoicePaymentDate form-control" value="' + timeVal + '"/>' +
                '</form>'
            );
            modal.setOkEvent(function () {
                $.ajax({
                    url: '/blueseal/xhr/FriendOrderPayInvoices',
                    method: 'POST',
                    dataType: 'json',
                    data: {row: row, date: $('#invoicePaymentDate').val(), amount: $('#billAmount').val() }
                }).done(function (res) {
                    modal.writeBody(res.message);
                    if (!res.error) {
                        dataTable.ajax.reload(null, false);
                        modal.setOkEvent(function () {
                            modal.hide();
                            dataTable.ajax.reload(null, false);
                        });
                    }
                }).fail(function (res) {
                    modal.writeBody("OOPS! C'è stato un problemino, se il problema persiste contatta un amministratore");
                    console.error(res);
                }).always(function () {
                    modal.setOkEvent(function () {
                        modal.hide();
                    });
                });
            });
        }
    }).fail(function(res){
        modal.writeBody('OOPS! C\'è stato un problema. Se il problema persiste contatta un amministratore.');
        modal.setOkEvent(function(){
            modal.hide()
        });
        console.error(res);
    })
});

$(document).on('bs.orderline.editPaymentBillAddInvoice', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno una fattura da aggiungere all'invoice"
        });
        return false;
    }

    var row = [];
    var i = 0;
    $.each(selectedRows, function (k, v) {
        row[i] = v.id;
        i++;
    });

    modal = new $.bsModal('Aggiungi una fattura alla distinta', {
        body: '<div class="form-group">' +
        '<label for="idBill">id distinta:</label>' +
        '<input type="text" name="idBill" class="idBill form-control" id="idBill" />' +
        '</div>',
        isCancelButton: true,
        okButtonEvent: function(){
            $.ajax({
                url: '/blueseal/xhr/FriendOrderPayInvoices',
                method: 'put',
                data:{row: row, action: 'add', idBill: $('#idBill').val()}
            }).done(function(res){
                modal.writeBody(res);
            }).fail(function(res){
                modal.writeBody('OOPS! C\'è stato un problema. Ritenta tra qualche minuto, se persiste un amministratore');
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