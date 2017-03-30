window.buttonSetup = {
    tag: "a",
    icon: "fa-compass",
    permission: "/admin/product/delete&&allShops",
    event: "bs.shipment.tracking.update",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Midifica Codici Ordine",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.shipment.tracking.update', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un ordine per inserire il tracker"
        }).open();
        return false;
    }

    let shipmentId = selectedRows[0].DT_RowId;

    let modal = new $.bsModal('Modifica Spedizione', {});

    Pace.ignore(function () {
        $.ajax({
            method: "GET",
            url: "/blueseal/xhr/ShipmentManageController",
            data: {
                shipmentId: shipmentId
            },
            dataType: "json"
        }).done(function (res) {
            let dayD = res.predictedDeliveryDate ? res.predictedDeliveryDate.slice(0,10) : "";
            let dayS = res.predictedShipmentDate ? res.predictedShipmentDate.slice(0,10) : "";
            modal.writeBody(
                '<div class="row">' +
                '<label for="bookingNumber">Booking Number</label>' +
                '<input autocomplete="off" type="text" id="bookingNumber" ' +
                    'class="form-control" name="bookingNumber" value="">' +
                '<label for="trackingNumber">Tracking Number</label>' +
                '<input autocomplete="off" type="text" id="trackingNumber" ' +
                    'class="form-control" name="trackingNumber" value="">' +
                '<label for="note">Note</label>' +
                '<input autocomplete="off" type="text" id="note" ' +
                    'class="form-control" name="note" value="">' +
                '<div class="row">' +
                '</div>' +
                '<div class="col-xs-6>">' +
                '<label for="predictedShipmentDate">Data Prevista di Partenza</label>' +
                '<input autocomplete="off" type="date" id="predictedShipmentDate" ' +
                'class="form-control" name="predictedShipmentDate" value="'+dayS+'">' +
                '</div>'+
                '<div class="col-xs-6>">' +
                '<label for="predictedDeliveryDate">Data Prevista di Arrivo</label>' +
                '<input autocomplete="off" type="date" id="predictedDeliveryDate" ' +
                'class="form-control" name="predictedDeliveryDate" value="'+dayD+'">' +
                '</div>'
            );
            $('#bookingNumber').val(res.bookingNumber);
            $('#trackingNumber').val(res.trackingNumber);
            $('#note').val(res.note);

            modal.setOkEvent(function () {
                res.bookingNumber = $('#bookingNumber').val();
                res.trackingNumber = $('#trackingNumber').val();
                res.note = $('#note').val();
                res.predictedShipmentDate = $('#predictedShipmentDate').val();
                res.predictedDeliveryDate = $('#predictedDeliveryDate').val();
                modal.showLoader();
                modal.setOkEvent(function () {
                    modal.hide();
                    $('.table').DataTable().ajax.reload(null, false);
                });
                $.ajax({
                    method: "put",
                    url: "/blueseal/xhr/ShipmentManageController",
                    data: {
                        shipment: res
                    },
                }).done(function (res2) {
                    modal.writeBody(res2);
                }).fail(function () {
                    modal.writeBody('Errore');
                });
            });
        });
    });
});
