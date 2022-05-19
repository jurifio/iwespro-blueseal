window.buttonSetup = {
    tag: "a",
    icon: "fa-truck fa-plus",
    permission: "/admin/order/list&&allShops",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi una nuova spedizione",
    placement: "bottom",
    event: "btn-add-shipmentToUs"
};

$(document).on('btn-add-shipmentToUs', function (a, b, c) {
    "use strict";

    let today = new Date().toISOString().slice(0, 10);
    let modal = new $.bsModal('Aggiungi una nuova spedizione verso di noi', {
            body:
                    '<label for="addressBook">Da</label>' +
                    '<select id="addressBook" class="full-width selectize" name="addressBook"></select>' +
                    '<label for="carrierSelect">Seleziona il vettore</label><br />' +
                    '<select id="carrierSelect" name="carrierSelect" class="full-width selectize"></select><br />' +
                    '<label for="shipmentDate">Data di Partenza</label>' +
                    '<input autocomplete="off" type="date" id="shipmentDate" ' +
                    'class="form-control" name="shipmentDate" value="' + today + '">'
        }
    );

    let addressSelect = $('select[name=\"addressBook\"]');
    let carrierSelect = $('select[name=\"carrierSelect\"]');

    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/FriendAccept',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            addressSelect.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.shopTitle) + '</span> - ' +
                            '<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.shopTitle) + '</span>  - ' +
                            '<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
                            '</div>'
                    }
                }
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
        modal.setOkEvent(function () {
            modal.hide();
            $('.table').DataTable().ajax.reload(null, false);
        });
        let date = $('#shipmentDate').val();
        let carrier = $('#carrierSelect').val();
        let fromAddress= $('#addressBook').val();
        modal.showLoader();
        $.ajax({
            method: "post",
            url: "/blueseal/xhr/ShipmentManageController",
            data: {
                shipmentDate: date,
                fromAddressId: fromAddress,
                carrierId: carrier
            },
            dataType: "json"
        }).done(function (res) {
            modal.writeBody('Creata distinta numero: ' + res.id);
        });
    });
});