window.buttonSetup = {
    tag: "a",
    icon: "fa-ship",
    permission: "/admin/product/delete&&allShops",
    event: "bs.order.shipment.prepare",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Invia Codice Tracker",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.order.shipment.prepare', function (e, element, button) {

    const rows = $.getDataTableSelectedRowsData(null,null,1);
    if (rows === false || rows.length === 0) return;
    let modal = new $.bsModal('Preparazione Spedizioni', {
        body: '<form>' +
                '<div class="form-group form-group-default">' +
                    '<label for="carrier">Vettore</label>' +
                    '<select id="carrier" name="carrier" class="full-width"></select>' +
                '</div>' +
              '</form>'
    });
    modal.hideOkBtn();
    modal.showCancelBtn();
    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Carrier'
            },
            dataType: 'json'
        }).done(function (res2) {
            let select = $('select[name=\"carrier\"]');
            if (select.length > 0 && typeof select[0].selectize !== 'undefined') select[0].selectize.destroy();
            let selectized = select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });

            selectized[0].selectize.on('change', function () {
                modal.hideOkBtn();
                $('#tracking').closest('div').remove();
                if (typeof this.items[0] === 'undefined') return;
                let selected = this.items[0];
                let selectedObj = this.options[selected];

                if(selectedObj.implementation === null) {
                    //MANUALE
                    if(rows.length > 1) {
                        modal.appendBody('<p>Non è possibile gestire più ordini con spedizioni manuali contemporaneamente</p>');
                    } else {
                        modal.getElement().find('form').append(
                                '<div class="form-group form-group-default required">' +
                                '<label for="tracking">TrackingNumber</label>' +
                                '<input id="tracking" name="tracking" autocomplete="off" class="form-control" value="" required="required">' +
                            '</div>');
                        modal.showOkBtn();
                    }

                } else {
                    //AUTOMATICO
                    modal.showOkBtn();
                }
            });
        });


        modal.setOkEvent(function () {
            let form = modal.getElement().find('form').serializeObject();
            form.ordersId = rows;
            modal.showLoader();
            Pace.ignore(function () {
                $.ajax({
                    method: 'POST',
                    url: '/blueseal/xhr/PrepareOrderShipment',
                    data: form
                }).done(function(res) {
                    "use strict";

                    let html = '<div class="print-list">';

                    res = JSON.parse(res);
                    for(let i in res.orders) {
                        if(!res.orders.hasOwnProperty(i)) continue;
                        html += '<a target="_blank" href="/blueseal/xhr/InvoiceAjaxController?orderId='+res.orders[i].id+'">Fattura Ordine '+res.orders[i].id+'</a><br />';
                    }

                    for(let i in res.shipments) {
                        if(!res.shipments.hasOwnProperty(i)) continue;
                        html += '<a target="_blank" href="/blueseal/xhr/PrintOrderShipmentLabel?shipmentId='+res.shipments[i].id+'">Etichetta '+res.shipments[i].id+'</a><br />';
                    }
                    html += '</div>';
                    modal.writeBody(html);
                    modal.hideCancelBtn();
                }).fail(function(res) {
                    "use strict";
                    modal.hideOkBtn();
                    modal.writeBody('Errore');
                }).always(function () {
                    modal.setOkEvent(function () {
                        modal.hide();
                        dataTable.ajax.reload();
                    });
                });
            })
        });
    });

});
