;(function () {

    $(document).on('bs.end.product.batch', function () {

        //Prendo tutti i lotti selezionati
        let productsBatch = [];
        let foisons = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        $.each(selectedRows, function (k, v) {
            productsBatch.push(v.row_id);
            foisons.push(v.foisonEmail)
        });

        let bsModal = new $.bsModal('Chiudi lotto', {
            body: '<p>Chiudere il lotto in via definitiva?</p>'
        });


        const data = {
            productBatchIds: productsBatch,
            foisons: foisons
        };

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductBatchManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });




    $(document).on('bs.end.invoice.product', function () {


        var datatable = $('.table').DataTable();
        var selectedRows = datatable.rows('.selected').data();

        var selectedRowsCount = selectedRows.length;

        var i = 0;
        var rows = [];
        $.each(selectedRows, function (k, v) {
            rows.push(v.row_id);
        });

        //Far scegliere
        modal = new $.bsModal(
            'Registrazione fattura per i pagamenti delle righe d\'ordine',
            { body: '<div id="invoiceType">' +
                '<select id="productBatchInvoiceType">' +
               ' <option disabled selected value>Seleziona un\'opzione</option>' +
                '<option value="1">Fattura</option>' +
                '<option value="2">Prestazione occasionale</option>' +
                '<option value="3">Ricevuta</option>' +
                '</select>' +
                '</div>'
            }
        );

        $('#productBatchInvoiceType').change(function () {
            let invTyp = $('#productBatchInvoiceType').val();

            const data = {
                rows: rows,
                invoiceCase: invTyp
            };

            $.ajax({
                url: '/blueseal/xhr/ProductBatchInvoiceManage',
                method: 'GET',
                dataType: 'json',
                data: data
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

                    invoiceTable +=
                        '<tr>' +
                        '<td style="text-align: right;">' +
                        'Imponibile lotti' +
                        '</td>' +
                        '<td style="text-align: right;">' + res.imponibile + '</td>' +
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
                        '<input type="hidden" id="userId" name="userId" value="' + res.user + '" />' +
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
                        var userId = $('#userId').val();
                        var invoiceFile = $('#invoiceFile').prop('files')[0];
                        var invoiceTotal = $('#invoiceTotal').val();
                        var dataPost = new FormData();
                        dataPost.append('rows', rows);
                        dataPost.append('date', invoiceDate);
                        dataPost.append('number', invoiceNumber);
                        dataPost.append('userId', userId);
                        dataPost.append('file', invoiceFile);
                        dataPost.append('total', invoiceTotal);
                        dataPost.append('invTyp', invTyp);
                        $.ajax({
                            url: '/blueseal/xhr/ProductBatchInvoiceManage',
                            cache: false,
                            contentType: false,
                            processData: false,
                            method: 'post',
                            dataType: 'json',
                            data: dataPost
                        }).done(function(res){
                            if (res.error) {
                                var alert = $('.alert');
                                alert.addClass('.alert-danger');
                                alert.html(res.responseText);
                            } else {
                                modal.writeBody(res.responseText);
                                modal.setOkEvent(function () {
                                    $.refreshDataTable();
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
    });




    $(document).on('bs.accept.product.batch', function () {


        let productsBatch = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        $.each(selectedRows, function (k, v) {
            productsBatch.push(v.row_id);
        });

        let bsModal = new $.bsModal('Accetta lotti', {
            body: '<p>Vuoi accettare i lotti selezionati?</p>'
        });


        const data = {
            productBatchIds: productsBatch,
        };

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ProductBatchAccept',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });


})();