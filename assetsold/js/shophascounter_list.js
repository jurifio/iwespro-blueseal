/**
 * Revisioned by Juri Fiorani after Created by Fabrizio Marconi on 22/10/2015.
 */
$(document).on('bs.shophascounter.modify', function () {

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

    var counterId = selectedRows[0].DT_RowId;
    var receiptCounter=selectedRows[0].receiptCounter;
    var invoiceCounter=selectedRows[0].invoiceCounter;
    var invoiceExtraUeCounter=selectedRows[0].invoiceExtraUeCounter;


    modal = new $.bsModal('Modifica I progressivi!',
        {
            body: '<p>Modifica I Sezionali</p>' +
                '<div class="row">' +
                '<div class="col-xs-6>">' +
                '<label for="receiptCounter">Ultimo Numero Ricevuta</label>' +
                '<input autocomplete="on" type="text" id="receiptCounter" ' +
                'class="form-control" name="receiptCounter" value="' + selectedRows[0].receiptCounter + '">' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-xs-6>">' +
                '<label for="invoiceCounter">Ultimo Numero Fattura Ue</label>' +
                '<input autocomplete="off" type="text" id="invoiceCounter"' +
                'class="form-control" name="invoiceCounter" value="' + selectedRows[0].invoiceCounter + '">' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-xs-6>">' +
                '<label for="invoiceExtraUeCounter">Ultimo Numero Fattura Extra Ue</label>' +
                '<input autocomplete="off" type="text" id="invoiceExtraUeCounter"' +
                'class="form-control" name="invoiceExtraUeCounter" value="' + selectedRows[0].invoiceExtraUeCounter + '">' +
                '</div>' +
                '</div>',
            okButtonEvent: function () {
                $.ajax(
                    {
                        url: '/blueseal/xhr/ShopHasCounterManage',
                        method: 'PUT',
                        data: {counterId: counterId,
                               receiptCounter:$('#receiptCounter').val(),
                                invoiceCounter:$('#invoiceCounter').val(),
                                invoiceExtraUeCounter:$('#invoiceExtraUeCounter').val()
                        }
                    }
                ).done(function (res) {
                    modal.writeBody(res);
                }).fail(function (res) {
                    modal.writeBody('OOPS! C\'è stato un problema!');
                    console.error(res);
                }).always(function () {
                    modal.setOkEvent(function () {
                        modal.hide();
                        $.refreshDataTable();
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
                '<td align="center"><b>ordine</b></td><td align="center"><b>Cliente</b></td><td align="center"><b>Booking Number</b></td><td align="center"><b>Tracking Number</b></td><td align="center"><b>Carrier</b></td><td align="center"><b>Data Creazione</b></td><td align="center"><b>Spedizione</b></td><td align="center"><b>Consegna Prevista</b></td><td align="center"><b>Consegna Effettiva</b></td>' +
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
function openTrackEmail(orderId) {
    var modal = new $.bsModal('Elenco Comunicazioni', {
        body: 'Email'
    });


    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/GetTrackingEmailAjaxController',
            method: 'get',
            dataType: 'json',
            data: {orderId: orderId}
        }).done(function (res) {
            let bodyemail =
                '<table class="table">' +
                '<thead>' +
                '<tr>' +
                '<td align="center"><b>Ordine</b></td><td align="center"><b>oraInvio</b></td><td align="center"><b>Email Mittente</b></td><td align="center"><b>Email Destinatario</b></td><td align="center"><b>Mittente</b></td><td align="center"><b>Destinatario</b></td><td align="center"><b>Oggetto</b></td><td align="center"><b>link</b></td>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';
            for (let i in res) {
                    bodyemail += '<tr>' +
                        '<td align="center"><font color="blue"<b>' + orderId + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].oraInvio + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].sender + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].targets + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].from + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].to + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].subject + '</b></font></td>' +
                        '<td align="center"><font color="blue"<b>' + res[i].link + '</b></font></td>';


            }
            bodyemail +=
                '</tbody>' +
                '</table>';


            modal.body.append(bodyemail);
            modal.addClass('modal-wide');
            modal.addClass('modal-high');
        });
    });

}
function openTrackGlsDelivery(trackingNumber){

    let track=trackingNumber;
    let url='https://www.gls-italy.com/index.php?option=com_gls&task=track_e_trace.getSpedizioneWeblabeling&format=raw&cn=MC1108&rf='+track+'&lc=ita';
    window.open(
        url, "Gls Tracking",
        "height=768,width=1024,modal=yes,alwaysRaised=yes");

}
function reGenerate(trackingNumber){

    let track=trackingNumber;
    let url='https://www.gls-italy.com/index.php?option=com_gls&task=track_e_trace.getSpedizioneWeblabeling&format=raw&cn=MC1108&rf='+track+'&lc=ita';
    window.open(
        url, "Gls Tracking",
        "height=768,width=1024,modal=yes,alwaysRaised=yes");

}