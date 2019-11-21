/**
 * Created by Fabrizio Marconi on 22/10/2015.
 */
$(document).on('bs.order.delete.panic', function () {

    var getVarsArray = [];
    var dataTable = $('.table').DataTable();
    var selectedRows = dataTable.rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un solo prodotto"
        }).open();
        return false;
    }

    var orderId = selectedRows[0].DT_RowId;

    modal = new $.bsModal('Elimina fisicamente e in modo permanente l\'ordine!',
        {
            body: 'Proseguendo sarà eliminato per sempre l\'ordine <strong>' + orderId + '</strong>!<br />' +
                'Pensaci un momento prima di proseguire!',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/OrderDeleteCompleteAjaxController',
                        method: 'DELETE',
                        data: {orderId: orderId}
                    }
                ).done(function (res) {
                    modal.writeBody(res);
                }).fail(function (res) {
                    modal.writeBody('OOPS! C\'è stato un problema!');
                    console.error(res);
                }).always(function () {
                    modal.setOkEvent(function () {
                        modal.hide();
                        dataTable.ajax.reload();
                    });
                });
            }
        }
    );
});

function openTrackDelivery(trackingNumber) {
    var modal = new $.bsModal('Conferma Ordine', {
        body: 'tracking Number'
    });


    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/GetTrackingDeliveryAjaxController',
            method: 'get',
            dataType: 'json',
            data: {tracking: trackingNumber}
        }).done(function (res) {
            let bodyshipment =
                '<table class="table">' +
                '<thead>' +
                '<tr>' +
                '<td align="center">ordine</td><td align="center">Cliente </td><td align="center">Booking Number</td><td align="center">Tracking Number</td><td align="center">Data Creazione</td><td align="center">Spedizione</td><td align="center">Consegna Prevista</td><td align="center">Consegna Effettiva</td>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';
            for (let i in res) {
                if (i == 1) {

                    bodyshipment += '<tr>+' +
                        '<td align="center">' + res[i].orderId + '</td>' +
                        '<td align="center">' + res[i].customer + '</td>' +
                        '<td align="center">' + res[i].bookingNumber + '</td>' +
                        '<td align="center">' + res[i].trackingNumber + '</td>' +
                        '<td align="center">' + res[i].creationDate + '</td>' +
                        '<td align="center">' + res[i].shipmentDate + '</td>' +
                        '<td align="center">' + res[i].predictedDeliveryDate + '</td>' +
                        '<td align="center">' + res[i].deliveryDate + '</td></tr>';

                }
            }
            bodyshipment +=
                '</tbody>' +
                '</table>';
            bodyshipment +=
                '<table class="table">' +
                '<thead>' +
                '<tr>' +
                '<td align="center">Data</td><td align="center">Posizione</td><td align="center"></td><td align="center">Nazione</td><td align="center">Descrizione</td>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';
            for (let s in res) {
                    bodyshipment += '<tr>+' +
                        '<td align="center">' + res[s].DateTime + '</td>' +
                        '<td align="center">' + res[s].City + '</td>' +
                        '<td align="center">' + res[s].CountryCode + '</td>' +
                        '<td align="center">' + res[s].Description + '</td></tr>';

            }
            bodyshipment +=
                '</tbody>' +
                '</table>';

            modal.body.append(bodyshipment);
        });
    });

}