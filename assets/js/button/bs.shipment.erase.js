window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/friend/order",
    event: "bs-shipment-erase",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella una spedizione Definitivamente",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-shipment-erase', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();

    if (1 != selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una e una sola spedizione"
        }).open();
        return false;
    }

    var shipmentId = selectedRows[0].DT_RowId;
    let modal = new $.bsModal('Segnala Partenza Spedizione', {
        body: 'Attenzione saranno cancellati definitivamente tutti i riferimenti a questa spedizione'
    });


    modal.writeBody(body);
    modal.setOkEvent(function () {

        $.ajax({
            method: "delete",
            url: "/blueseal/xhr/ShipmentDeleteAjaxController",
            data: {
                shipmentId: shipmentId
            }
        }).done(function (res) {
            modal.writeBody(res);
            modal.hideCancelBtn();
        }).fail(function (res) {
            modal.writeBody(res);
        }).always(function () {
            modal.setOkEvent(function () {
                modal.hide();
                dataTable.ajax.reload();
            });
        });

    });
});
