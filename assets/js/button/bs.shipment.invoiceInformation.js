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

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();
    if (selectedRows.length === 1) {

        var idShipment = selectedRows[0].id;
        var fromAddress = selectedRows[0].fromAddress;
        var toAddress = selectedRows[0].toAddress;
        var trackingNumber = selectedRows[0].trackingNumber;


        let bsModal = new $.bsModal('Info su fattura di spedizione', {
            body: '<div><p>Aggiugi informazioni realative alla fatturazione della spedizione n. <strong>'+ idShipment +'</strong></p>' +
            '<p><strong>INVIATO DA:</strong></p>'+ fromAddress +'</p>' +
            '<p><strong>A</strong></p>' + toAddress + '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label for="shipmentInvoiceNumber">Numero fattura di spedizione</label>' +
            '<input autocomplete="off" type="text" id="shipmentInvoiceNumber" ' +
            'placeholder="Numero di fattura della spedizione" class="form-control" name="shipmentInvoiceNumber" required="required">' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label for="realShipmentPrice">Spesa effettiva della spedizione</label>' +
            '<input autocomplete="off" type="text" id="realShipmentPrice" ' +
            'placeholder="Spesa effettiva" class="form-control" name="realShipmentPrice" required="required">' +
            '</div>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                idShipment: idShipment,
                shipmentInvoiceNumber: $('input#shipmentInvoiceNumber').val(),
                realShipmentPrice: $('input#realShipmentPrice').val().replace(/,/g, '.'),
                trackingNumber: trackingNumber
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ShipmentInvoiceController',
                data: data
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
    } else if (selectedRows.length < 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga"
        }).open();
        return false;
    } else {
        new Alert({
            type: "warning",
            message: "Puoi cancellare solamente un macrogruppo alla volta"
        }).open();
        return false;
    }

});