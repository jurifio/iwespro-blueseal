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
    var modal = new $.bsModal('Dettagli di Spedizione', {
        body: 'tracking Number'
    });


    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/GetTrackingDeliveryAjaxController',
            method: 'get',
            dataType: 'json',
            data: {trackingNumber: trackingNumber}
        }).done(function (res) {
            let bodyshipment =
                '<table class="table">' +
                '<thead>' +
                '<tr>' +
                '<td align="center"><b>ordine</b>></td><td align="center"><b>Cliente</b></td><td align="center"><b>Booking Number</b></td><td align="center"><b>Tracking Number</b></td><td align="center"><b>Carrier</b></td<td align="center"><b>Data Creazione</b></td><td align="center"><b>Spedizione</b></td><td align="center"><b>Consegna Prevista</b></td><td align="center"><b>Consegna Effettiva</b></td>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';
            for (let i in res) {
                if (i == 0) {

                    bodyshipment += '<tr>' +
                        '<td align="center"><font color="blue"<b>' + res[i].orderId + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].customer + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].bookingNumber + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].trackingNumber + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].carrier + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].creationDate + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].shipmentDate + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].predictedDeliveryDate + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].deliveryDate + '</b></font></td></tr>';

                }
            }
            bodyshipment +=
                '</tbody>' +
                '</table>';
            bodyshipment +=
                '<table class="table">' +
                '<thead>' +
                '<tr>' +
                '<td align="center"><b>Data</b></td><td align="center"><b>Posizione</b></td><td align="center"><b>Nazione</b></td><td align="center"><b>Descrizione</b></td>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';
            for (let s in res) {
                    bodyshipment += '<tr>' +
                        '<td align="center"><font color="blue"<b>' + res[s].DateTime + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[s].City + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[s].CountryCode + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[s].Description + '</b></font></td></tr>';

            }
            bodyshipment +=
                '</tbody>' +
                '</table>';

            modal.body.append(bodyshipment);
            modal.addClass('modal-wide');
            modal.addClass('modal-high');
        });
    });

}