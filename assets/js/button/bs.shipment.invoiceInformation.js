window.buttonSetup = {
    tag: "a",
    icon: "fa-keyboard-o",
    permission: "/admin/friend/order",
    event: "bs-add-invoice-shipping-info",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi i dati di fatturazione della spedizione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-add-invoice-shipping-info', function () {
    $.ajax({
        url: '/blueseal/xhr/SelectLastShipmentInvoiceAjaxController',
        method: 'get',
        data: {
            shipment: 1
        },
        dataType: 'json'
    }).done(function (res) {
        let rawShipment = res;

        $.each(rawShipment, function (k, v) {
            $("#shipmentInvoiceNumberTemp").val(v.shipmentInvoiceNumber);
            $("#lastInvoiceDate").val(v.dateInvoice);

            // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
    });

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();
    var lastInvoiceNumber=$("#shipmentInvoiceNumberTemp").val();
    var lastInvoiceDate=$("#lastInvoiceDate").val();
    if (selectedRows.length === 1) {
        var idShipment = selectedRows[0].id;
        var fromAddress = selectedRows[0].fromAddress;
        var toAddress = selectedRows[0].toAddress;
        var trackingNumber = selectedRows[0].trackingNumber;
        var carrier=selectedRows[0].carrier;

            let bsModal = new $.bsModal('Consuntiva Costi di spedizione', {
                body: '<div><p>Aggiugi informazioni realative alla fatturazione della spedizione n. <strong>' + idShipment + '</strong></p></div>' +
                    '<div class="row"><div class="col-md-6"><strong>INVIATO DA:</strong><p>' + fromAddress + '</div>' +
                    '<div class="col-md-6"><strong>A</strong><p> ' + toAddress + '</div></div>' +
                    '<div class="row"><div class="col-md-12"><strong>CARRIER:' + carrier + '</strong></div></div>' +
                    '<div class="form-group form-group-default required">' +
                    '<label for="shipmentInvoiceNumber">Numero fattura di spedizione</label>' +
                    '<input autocomplete="off" type="text" id="shipmentInvoiceNumber" ' +
                    'placeholder="Numero di fattura della spedizione" class="form-control" name="shipmentInvoiceNumber" value="' + lastInvoiceNumber + '" required="required">' +
                    '</div>' +
                    '<div class="form-group form-group-default required">' +
                    '<label for="invoiceDate">Data Fattura Carrier</label>' +
                    '<input autocomplete="off" type="datetime-local" id="invoiceDate" ' +
                    'placeholder="Data Fattura" class="form-control" name="invoiceDate" value="' + lastInvoiceDate + '" required="required">' +
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
                    '</div>'
            });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                idShipment: idShipment,
                shipmentInvoiceNumber: $('#shipmentInvoiceNumber').val(),
                realShipmentPrice: $('input#realShipmentPrice').val().replace(/,/g, '.'),
                invoiceDate :$('input#invoiceDate').val(),
                isBilling : $('#isBilling').val(),
                trackingNumber: trackingNumber
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ShipmentInvoiceController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
                setTimeout(function(){
                    window.location.reload();
                }, 500);
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
    } else if (selectedRows.length < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga"
        }).open();
        return false;
    } else {
        new Alert({
            type: "warning",
            message: "Puoi aggiornare una riga alla volta"
        }).open();
        return false;
    }

});