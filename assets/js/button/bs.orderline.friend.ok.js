window.buttonSetup = {
    tag: "a",
    icon: "fa-thumbs-up",
    permission: "/admin/product/list",
    event: "bs.orderline.friend.ok",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Accetta le righe d'ordine selezionate",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.orderline.friend.ok', function () {
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

    var modal = new $.bsModal('Conferma Ordine', {
        body: '<label for="addressBook">Seleziona l\'indirizzo di ritiro</label><br />' +
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
                labelField: 'name',
                options: res
            });
        });
    });

    $(document).on('change', "select[name=\"addressBook\"], select[name=\"carrierSelect\"]", function () {
        if (!(addressSelect.val() && carrierSelect.val())) {
            shippingDate.attr('disabled', 'disabled');
            return false;
        }

        if (shippingDate.length == 0) {
            modal.body.append(
                '<label for="shippingDate">Seleziona Data Ritiro</label>' +
                '<select id="shippingDate" name="shippingDate" class="full-width selectize" disabled="disabled"></select><br />' +
                '<label for="bookingNumber">Inserisci il codice di ritiro (se presente)</label>' +
                '<input id="bookingNumber" name="bookingNumber" class="full-width" ><br />'
            );
            shippingDate = $('select[name=\"shippingDate\"]');
        }
        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/FriendShipment',
                data: {
                    fromAddressBookId: addressSelect.val(),
                    carrierId: carrierSelect.val()
                },
                dataType: 'json'
            }).done(function (res) {
                let opt = [];
                for (let i in res) {
                    opt.push(
                        {
                            "value": res[i]
                        });
                }
                shippingDate.selectize({
                    options: opt,
                    labelField: 'value',
                    valueField: 'value'
                });
                shippingDate[0].selectize.enable();
            });
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
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span>' +
                            ' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span>' +
                            ' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit) + '</span>' +
                            '</div>'
                    }
                }
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
        modal.setOkEvent(function () {
            "use strict";
            modal.hide();
            $('.table').DataTable().ajax.reload(null, false);
        });
        let bookingNubmer = $('#bookingNumber').val();
        modal.showLoader();
        $.ajax({
            url: '/blueseal/xhr/FriendAccept',
            method: 'POST',
            data: {
                rows: row,
                response: 'ok',
                fromAddressBookId: addressSelect.val(),
                carrierId: carrierSelect.val(),
                shippingDate: shippingDate.val(),
                bookingNumber: bookingNubmer
            }
        }).done(function (res) {
            modal.writeBody(res);
        }).fail(function (res) {
            modal.writeBody(res.responseText);
        });
    });

});