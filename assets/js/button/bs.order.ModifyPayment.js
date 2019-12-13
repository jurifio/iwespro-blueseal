window.buttonSetup = {
    tag:"a",
    icon:"fa-bank",
    permission:"/admin/product/edit&&allShops",
    event:"bs-order-ModifyPayment",
    class:"btn btn-default",
    rel:"tooltip",
    title:"MOdifica Metodo di Pagamento",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-order-ModifyPayment', function () {
    var orderId = $.QueryString['order'];

    var modal = new $.bsModal('Modifica Ordine ', {
        body: '<label for="orderPaymentMethodId">Seleziona Il Nuovo Metodo di Pagamento</label><br />' +
            '<select id="orderPaymentMethodId" name="orderPaymentMethodId" class="full-width selectize"></select><br />'
    });

    let orderPaymentMethodId = $('select[name=\"orderPaymentMethodId\"]');


    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/ChangePaymentAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            addressSelect.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });

    $(document).on('change', "select[name=\"addressBook\"], select[name=\"carrierSelect\"]", function () {
        if (!(addressSelect.val() && carrierSelect.val())) {
            shippingDate.attr('disabled', 'disabled');
            return false;
        }

        if (shippingDate.length === 0) {
            modal.body.append(
                '<label for="shippingDate">Seleziona Data Ritiro</label>' +
                '<select id="shippingDate" name="shippingDate" class="full-width selectize" disabled="disabled"></select><br />' +
                '<label for="bookingNumber">Inserisci il codice di ritiro (se presente)</label>' +
                '<input id="bookingNumber" name="bookingNumber" class="full-width" ><br />'
            );
            shippingDate = $('select[name=\"shippingDate\"]');
        }
        if (typeof (shippingDate[0].selectize) != 'undefined') shippingDate[0].selectize.destroy();

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
        let par = null;
        if(shopId == 1) {
            par = {id: 5};
        } else {
            par = {isActive: 1, isForPickUp: 1}
        }
        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Carrier',
                condition: par
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
                            ' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit === null ? 'Variabile' : item.prenotationTimeLimit) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span>' +
                            ' - <span class="caption">Limite prenotazione: ' + escape(item.prenotationTimeLimit === null ? 'Variabile' : item.prenotationTimeLimit) + '</span>' +
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
            res = JSON.parse(res);
            var x = '<p>' + res.message + '</p><br />' +
                '<strong style="color:red">RICORDATI DI STAMPARE ED APPLICARE L\'ETICHETTA AL COLLO!</strong><br />';
            x += typeof res.shipmentId === 'undefined' ? '' : '<a target="_blank" href="/blueseal/xhr/FriendShipmentLabelPrintController?shipmentId=' + res.shipmentId + '">Stampa Etichetta</a>';
            modal.writeBody(x);
        }).fail(function (res) {
            modal.writeBody(res.responseText);
        });
    });

});