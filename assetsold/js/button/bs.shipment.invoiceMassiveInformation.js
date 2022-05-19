window.buttonSetup = {
    tag: "a",
    icon: "fa-align-justify",
    permission: "/admin/friend/order",
    event: "bs-add-invoice-shippingmassive-info",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiorna  i dati di fatturazione delle Spedizioni",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-add-invoice-shippingmassive-info', function () {

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shipment'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#trackingNumberFind');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'trackingNumber',
            searchField:'trackingNumber',
            options: res2,
        });

    });




            let bsModal = new $.bsModal('Consuntiva Costi di spedizione ', {
                body: '<div><p>ricerca Spedizioni</p></div>' +

                    '<div class="row"><div class="col-md-12">'+
                    '<div class="form-group form-group-default selectize-enabled">' +
                    '<label for="trackingNumberFind">Seleziona il TrackingNumber </label>'+
                    '<select id="trackingNumberFind" name="trackingNumberFind"'+
                    'class="full-width selectpicker"' +
                    'placeholder="Seleziona la Lista"' +
                    'data-init-plugin="selectize">'+
                    '</select>'+
                    '</div>'+
                    '<div class="form-group form-group-default required">' +
                    '<label for="shipmentInvoiceNumber">Numero fattura di spedizione</label>' +
                    '<input autocomplete="off" type="text" id="shipmentInvoiceNumber" ' +
                    'placeholder="Numero di fattura della spedizione" class="form-control" name="shipmentInvoiceNumber" value="" required="required">' +
                    '</div>' +
                    '<div class="form-group form-group-default required">' +
                    '<label for="invoiceDate">Data Fattura Carrier</label>' +
                    '<input autocomplete="off" type="datetime-local" id="invoiceDate" ' +
                    'placeholder="Data Fattura" class="form-control" name="invoiceDate" value="" required="required">' +
                    '</div>' +
                    '<div class="form-group form-group-default required">' +
                    '<label for="realShipmentPrice">Spesa effettiva della spedizione</label>' +
                    '<input autocomplete="off" type="text" id="realShipmentPrice" ' +
                    'placeholder="Spesa effettiva" class="form-control" name="realShipmentPrice" required="required">' +
                    '</div>' +
                    '<div class="form-group form-group-default required hide">' +
                    '<label for="isBilling">Rifatturata</label>' +
                    '<select name="isBilling"  placeholder="seleziona se rifatturata" id="isBilling">' +
                    '<option value="">Seleziona</option>' +
                    '<option value="1" selected="selected" >Si</option>' +
                    '<option value="0">No</option>' +
                    '</select>' +
                    '</div>'+
                    '<div id="result"></div>'
            });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                trackingNumber: $('#trackingNumberFind').val(),
                shipmentInvoiceNumber: $('#shipmentInvoiceNumber').val(),
                realShipmentPrice: $('input#realShipmentPrice').val().replace(/,/g, '.'),
                invoiceDate :$('input#invoiceDate').val(),
                isBilling : $('#isBilling').val(),
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ShipmentInvoiceController',
                data: data
            }).done(function (res) {
               $('#result').append(res);
                var selectize = $("#trackingNumberFind")[0].selectize;
                selectize.clear();
                document.getElementById('realShipmentPrice').value = '';

            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {

            });
        });



});