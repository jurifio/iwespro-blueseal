window.buttonSetup = {
    tag:"a",
    icon:"fa-money",
    permission:"/admin/product/edit",
    event:"bs-friend-order-registerInvoiceFromFile",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Associa fattura alle righe dell'ordine",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-friend-order-registerInvoiceFromFile', function () {
    var datatable = $('.table').DataTable();
    var selectedRows = datatable.rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    modal = new $.bsModal(
        'Registrazione fattura per i pagamenti delle righe d\'ordine',
        { body: ''}
    );

    if (1 > selectedRowsCount) {
        modal.writeBody('<p><strong>Attenzione:</strong></p><p>Per procedere, seleziona prima le righe d\'ordine associate alla fattura che stai per inserire</p>');
        return false;
    }

    var i = 0;
    var rows = [];
    $.each(selectedRows, function (k, v) {
        rows.push(v.orderCode);
    });

    $.ajax({
        url: '/blueseal/xhr/FriendOrderRecordInvoice',
        method: 'GET',
        dataType: 'json',
        data: {rows}
    }).fail(function(res){
        modal.writeBody('OOPS! C\'è stato un problema!<br />Ritenta tra uno o due minuti. Se il problema persiste contattaci');
        console.error(res);
    }).done(function(res){
        modal.writeBody(res.responseText);
        if (!res.error) {
            var invoiceTable =
                '<table class="table">' +
                '<thead>' +
                '<tr>' +
                '<th>Descrizione</th>' +
                '<th>Prezzo</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';

            for(var i in res.lines) {
                var line = res.lines[i];

                invoiceTable +=
                    '<tr>' +
                        '<td><span class="small">' + line.description + '</span></td>' +
                        '<td style="text-align: right;">' + line.friendRevenue + '</td>' +
                    '</tr>';
            }
            invoiceTable +=
                '<tr>' +
                '<td style="text-align: right;">' +
                'Imponibile da ordini' +
                '</td>' +
                '<td style="text-align: right;">' + res.totalNoVat + '</td>' +
                '</tr>';

            invoiceTable +=
                '<tr>' +
                '<td style="text-align: right;">' +
                'Totale IVA' +
                '</td>' +
                '<td style="text-align: right;">' + res.vat + '</td>' +
                '</tr>';
            invoiceTable +=
                '<tr>' +
                '<td style="text-align: right; font-weight: bold">' +
                'Totale fattura da ordini' +
                '</td>' +
                '<td style="text-align: right; font-weight: bold">' + res.total + '</td>' +
                '</tr>';
            invoiceTable+= '</tbody>' +
                '</table>';
            var now = new Date();
            var day = ("0" + now.getDate()).slice(-2);
            var month = ("0" + (now.getMonth() + 1)).slice(-2);
            var today = now.getFullYear()+ "-" + (month) + "-" + (day) ;
            var timeVal = (res.time) ? res.time : today;

            var invoiceForm = '<form id="sendInvoiceWithFile">' +
                '<div class="alert"></div>' +
                '<input type="hidden" id="invoiceShop" name="invoiceShop" value="' + res.billingAddressBookId + '" />' +
                '<div class="row">' +
                    '<div class="col-sm-12">' +
                        '<div class="form-group">' +
                        '<label for="invoiceFile">File</label>' +
                        '<input type="file" class="form-control" id="invoiceFile" name="invoiceFile">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-sm-6">'+
                        '<div class="form-group">' +
                        '<label for="invoiceDate">Data Emissione</label>' +
                        '<input type="date" class="form-control" id="invoiceDate" name="invoiceDate" value="' + timeVal + '" />' +
                        '</div>' +
                    '<div class="col-sm-6">' +
                        '<div class="form-group">' +
                        '<label for="invoiceNumber">Numero Fattura</label>' +
                        '<input type="text" class="form-control" id="invoiceNumber" name="invoiceNumber" />' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="row">' +
                    '<div class="col-sm-6">' +
                        '<div class="form-group">' +
                        '<label for="invoiceTotalPreview">Totale fattura da ordine</label>' +
                        '<input type="text" class="form-control" id="invoiceTotalPreview" name="invoiceTotalPreview" value="' + res.total +'" readonly/>' +
                        '</div>' +
                    '<div class="col-sm-6">' +
                        '<div class="form-group">' +
                        '<label for="invoiceTotal">Totale fattura da friend</label>' +
                        '<input style="" type="text" class="form-control inputPrice" id="invoiceTotal" name="invoiceTotal" value="' + res.total +'" />' +
                        '</div>' +
                    '</div>';
                '</div>'
                '</form>';

            var body = '<h4>Riepilogo dei prodotti selezionati</h4>';
            body+= invoiceTable;
            body+= '<h5 style="padding-top: 30px;">Inserisci i dati della fattura qui di seguito.</h5>';
            body+= invoiceForm;

            modal.writeBody(body);

            modal.setOkEvent(function(){
                var invoiceDate = $('#invoiceDate').val();
                var invoiceNumber = $('#invoiceNumber').val();
                var invoiceShop = $('#invoiceShop').val();
                var invoiceFile = $('#invoiceFile').prop('files')[0];
                var invoiceTotal = $('#invoiceTotal').val();
                var data = new FormData();
                data.append('rows', rows);
                data.append('date', invoiceDate);
                data.append('number', invoiceNumber);
                data.append('shopId', invoiceShop);
                data.append('file', invoiceFile);
                data.append('total', invoiceTotal);
                $.ajax({
                    url: '/blueseal/xhr/FriendOrderRecordInvoice',
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'post',
                    dataType: 'json',
                    data: data
                }).done(function(res){
                    if (res.error) {
                        var alert = $('.alert');
                        alert.addClass('.alert-danger');
                        alert.html(res.responseText);
                    } else {
                        modal.writeBody(res.responseText);
                        modal.setOkEvent(function () {
                            modal.hide();
                        });
                    }
                }).fail(function(res) {
                    var alert = $('.alert');
                    alert.addClass('.alert-danger');
                    alert.html('OOPS! C\'è stato un problema!<br />Ritenta tra uno o due minuti. Se il problema persiste contattaci');
                    console.error(res);
                });
            });
        }
    });
});