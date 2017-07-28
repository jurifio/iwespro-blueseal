window.buttonSetup = {
    tag: "a",
    icon: "fa-times",
    permission: "/admin/friend/order",
    event: "bs.shipment.cancel",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella una spedizione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.shipment.cancel', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();

    let selectedRows = dataTable.rows('.selected').data();

    if (1 != selectedRows.length) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una e una sola spedizione"
        }).open();
        return false;
    }
    if ((selectedRows[0].cancellationDate && selectedRows[0].cancellationDate.length != 0) || (selectedRows[0].shipmentDate && selectedRows[0].shipmentDate.length != 0)) {
        new Alert({
            type: "warning",
            message: "Ordine spedito o già cancellato"
        }).open();
        return false;
    }

    var shipmentId = selectedRows[0].DT_RowId;
    let modal = new $.bsModal('Segnala Partenza Spedizione', {
        body: ''
    });
    modal.showLoader();
    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        dataType: 'json',
        data: {table: 'ShipmentFault', fields: ['id', 'description'], condition: {userType: 'friend'}},
    }).fail(function () {
        modal.writeBody('Non riesco a recuperare le informazioni');
    }).done(function (res) {
        let options = '<option value="" readonly selected>Seleziona una voce</option>';
        for(let i in res) {
            options += '<option value="' + res[i].id + '">' + res[i].description + '</option>';
        }
        let body = '<div class="row form-group">' +
            '<p>Elimina la spedizione e creane una nuova per il giorno successivo al giorno successivo</p>' +
            '<div class="col-xs-12 selectize-enabled">' +
            '<label for="faultId">Seleziona il motivo:</label>' +
            '<select id="faultId" class="form-control" name="faultId">' + options + '</select>' +
            '</div>'+
            '<div class="col-xs-12">' +
            '<label for="newShipmentDate">Seleziona la data per la nuova spedizione:</label>' +
            '<input id="newShipmentDate" class="form-control" name="newShipmentDate" type="date">' +
            '</div>';
        ;

        modal.writeBody(body);
        modal.setOkEvent(function () {
           let selectFault = $('#faultId').val();
           let newShipmentDate = $('#newShipmentDate').val();
           if (selectFault) {
               $.ajax({
                   method: "delete",
                   url: "/blueseal/xhr/ShipmentManageController",
                   data: {
                       shipmentId: shipmentId, faultId: selectFault, newShipmentDate: newShipmentDate
                   }
               }).done(function (res) {
                   modal.writeBody(res);
                   modal.hideCancelBtn();
               }).fail(function (res) {
                   if (res.exception == 'shipment') {
                       res = JSON.parse(res);
                       modal.writeBody('<strong>Errore!</strong> ' + res['message']);
                   } else {
                       modal.writeBody('<strong>Oops!</strong> C\'è stato un problema. Contatta un amministratore');
                       console.log(res.message);
                   }
               }).always(function () {
                   modal.setOkEvent(function () {
                       modal.hide();
                       dataTable.ajax.reload();
                   });
               });
           }
        });
    });
});
