$(document).on('bs.orderline.paymentToFriend', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.line_id;
        i++;
    });

    $.ajax({
        url: '/blueseal/xhr/FriendOrderChangePaymentStatus',
        mode: 'GET',
        dataType: 'JSON',
        data: {orderLines: row}
    }).fail(function () {
        modal = new $.bsModal('Accettazione ordini',
            {
                body: 'OOPS! Non posso farti selezionare il pagamento ora.<br />' +
                'Probabilmente è un problema momentaneo. Riprova fra qualche minuto.'
            });
    }).done(function (res) {
        var opts = '';
        res.selected = 4;
        for (var i in res.options) {
            var statusId = res.options[i].id;
            opts += '<option value="' + statusId + '" ' + ((statusId == res.selected) ? 'selected' : '') + '>' + res.options[i].name + '</option>';
        }

        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear() + "-" + (month) + "-" + (day);
        var timeVal = (res.time) ? res.time : today;

        modal = new $.bsModal('Accettazione ordini',
            {
                body: '<div class="form-group">' +
                'Stato: <select  class="form-control" name="friendPaymentStatus" id="friendPaymentStatus">' +
                '<option value="0" disabled selected>Seleziona un\'opzione</option>' +
                opts +
                '</select>' +
                'Data pagamento: <input  value="' + timeVal + '" type="date" id="friendPaymentDate" class="form-control"/>' +
                '</div>',
                okButtonEvent: function () {
                    var newStatus = $('#friendPaymentStatus').val();
                    var date = $('#friendPaymentDate').val();
                    if (0 < newStatus) {
                        $.ajax({
                            url: '/blueseal/xhr/FriendOrderChangePaymentStatus',
                            method: 'POST',
                            data: {
                                orderLines: row,
                                friendPaymentStatus: newStatus,
                                friendPaymentDate: date
                            }
                        }).done(function (res) {
                            modal.writeBody(res);
                            modal.setOkEvent(function () {
                                modal.hide();
                                dataTable.ajax.reload();
                            });
                        }).fail(function (res) {
                            modal.writeBody("OOPS! C'è stato un problemino, se il problema persiste concattata un amministratore");
                            console.error(res);
                        });
                    }
                }
            }
        );
    });
});

$(document).on('bs.friend.orderline.ko', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.line_id;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    modal = new $.bsModal({
        header: 'Conferma Ordine'
    });

    $.ajax({
        url: '/blueseal/xhr/FriendAccept',
        method: 'POST',
        data: {rows: row, response: 'ko'},
    }).done(function (res) {
        res = JSON.parse(res);
        var x = '<p>' + res.message + '</p><br />';
        x += typeof res.shipmentId === 'undefined' ? '' : '<a target="_blank" href="/blueseal/xhr/FriendShipmentLabelPrintController?shipmentId=' + res.shipmentId + '">Stampa Etichetta</a>';
        modal.writeBody(x);
        $('.table').DataTable().ajax.reload(null, false);
    }).fail(function (res) {
        modal.writeBody(res.responseText);
    });
});

$(document).on('bs.friend.orderline.shippedByFriend', function () {
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();

    if (1 > selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    $.each(selectedRows, function (k, v) {
        row[i] = v.line_id;
        i++;
        //getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });

    modal = new $.bsModal('Segna le righe ordine come spedite',
        {
            body: 'Sto eseguendo l\'operazione. Attendi qualche istante...'
        });
    $.ajax({
        url: '/blueseal/xhr/FriendShipment',
        method: 'POST',
        dataType: 'JSON',
        data: {rows: row}
    }).done(function (res) {
        modal.writeBody(res.message);
    }).fail(function (res) {
        modal.writeBody('OOPS! C\'è stato un problema. Se il problema persiste contatta un amministratore');
        console.error(res);
    });
});

$(document).on('bs.remove.invoice', function () {

    var datatable = $('.table').DataTable();
    var selectedRows = datatable.rows('.selected').data();

    var selectedRowsCount = selectedRows.length;
    if (selectedRowsCount === 1) {
    let bsModal = new $.bsModal('Disassocia documenti', {
        body: '<p>Disassocia:</p>' +
        '<div>'+'' +
        '<select id="remove">' +
        '<option value="1">Fattura</option>' +
        '<option value="6">DDT</option>' +
        '</select>'+
        '</div>'
    });

    var rows = [];
    $.each(selectedRows, function (k, v) {
        rows.push(v.orderCode);
    });



        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            let selectedValue = $( "#remove" ).val();
            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/RemoveFriendInvoice',
                data: { rows,
                        selectedValue
                }
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    } else {
        new Alert({
            type: "warning",
            message: "Seleziona una sola riga"
        }).open();
    }
});


$(document).on('bs.change.price.friend', function () {
    let datatable = $('.table').DataTable();

    let selectedRows = datatable.rows('.selected').data();

    if(selectedRows.length !== 1){
        new Alert({
            type: "warning",
            message: "Seleziona solo UNA riga"
        }).open();
        return false;
    }

    let bsModal = new $.bsModal('Cambia prezzo FRIENDS', {
        body: `<p>Inserisci un nuovo prezzo</p>
                <input type="number" id="newFriendsPrice" step="any">`
    });

    let orderCode = selectedRows[0].orderCode;

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/ChangePriceFriendAjaxController',
            data: {
                order: orderCode,
                newPrice: $('#newFriendsPrice').val()
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                $.refreshDataTable();
                bsModal.hide();
                });
            bsModal.showOkBtn();
        });
    });
});