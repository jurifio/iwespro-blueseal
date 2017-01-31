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

$(document).on('bs.friend.orderline.ok', function () {
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
    $.each(selectedRows, function (k, v) {
        row.push(v.line_id);
    });

    modal = new $.bsModal('Conferma Ordine', {
        body:
        '<label for="addressBook">Seleziona l\'indirizzo di provenienza</label><br />' +
        '<select id="addressBook" name="addressBook" class="full-width selectize"></select><br />' +
        '<label for="carrierSelect">Seleziona il vettore</label><br />' +
        '<select id="carrierSelect" name="carrierSelect" class="full-width selectize"></select><br />'
    });

    let addressSelect = $('select[name=\"addressBook\"]');
    let carrierSelect = $('select[name=\"carrierSelect\"]');
    let shippingDate = $('select[name=\"shippingDate\"]');


    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/FriendAccept',
            method: 'get',
            data: {
                rows: row
            },
            dataType: 'json'
        }).done(function (res) {
            shippingDate.datepicker()
        });
    });

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/FriendAccept',
            method: 'get',
            data: {
                rows: row
            },
            dataType: 'json'
        }).done(function (res) {
            addressSelect.selectize({
                valueField: 'id',
                labelField: 'subject',
                options: res
            });
        });
    });

    $(document).on('change',"select[name=\"addressBook\"], select[name=\"carrierSelect\"]",function () {
        if(!(addressSelect.val() && carrierSelect.val())) {
            shippingDate.attr('disabled','disabled');
            return false;
        }

        if(shippingDate.length == 0) {
            modal.body.append(
                '<label for="shippingDate">Seleziona Data Ritiro</label>' +
                '<select id="shippingDate" name="shippingDate" class="full-width selectize" disabled="disabled"></select><br />' +
                '<label for="bookingNumber">Inserisci il codice di ritiro (se presente)</label>' +
                '<input id="bookingNumber" name="bookingNumber" class="full-width" ><br />'
            );
            shippingDate = $('select[name=\"shippingDate\"]');
        }

        $.ajax({
            url: '/blueseal/xhr/FriendShipment',
            data: {
                fromAddressBookId: addressSelect.val(),
                carrierId: carrierSelect.val()
            },
            dataType: 'json'
        }).done(function (res) {
            let opt = [];
            for(let i in res) {
                opt.push(
                    {
                        "value" : res[i],
                        "label" : res[i],
                        "name" : res[i]
                    });
            }
            shippingDate.selectize({
                options: opt
            });
            shippingDate[0].selectize.enable();
        });
    });

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Carrier'
            },
            dataType: 'json'
        }).done(function (res) {
            if (carrierSelect.length > 0 && typeof carrierSelect[0].selectize != 'undefined') carrierSelect[0].selectize.destroy();
            carrierSelect.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
            });
            carrierSelect[0].selectize.setValue(1);
        });
    });

    modal.setOkEvent(function () {
        if (!(addressSelect.val() && carrierSelect.val() && shippingDate.val())) {
            new Alert({
                type: "warning",
                message: "Devi selezionare l'indirizzo, il corriere e la data di ritiro"
            }).open();
            return false;
        }
        $.ajax({
            url: '/blueseal/xhr/FriendAccept',
            method: 'POST',
            data: {
                rows: row,
                response: 'ok',
                fromAddressBookId: addressSelect.val(),
                carrierId: carrierSelect.val(),
                bookingNumber: $('#bookingNumber').val()
            }
        }).done(function (res) {
            modal.writeBody(res);
            $('.table').DataTable().ajax.reload(null, false);
        }).fail(function (res) {
            modal.writeBody(res.responseText);
        });
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
        data: {rows: row, response: 'ko'}
    }).done(function (res) {
        modal.writeBody(res);
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