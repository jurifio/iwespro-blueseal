;(function () {

    $(document).on('bs.empty.product.batch', function () {
        let i = 0;
        let index = [];

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'WorkCategory'
            },
            dataType: 'json'
        }).done(function (res) {
            var select = $('#workCat');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                options: res
            });
        });


        let bsModal = new $.bsModal('Nuovo lotto', {
            body: `<p>Crea un lotto vuoto</p>
                    <div>
                        <p>Seleziona la categoria alla quale assocerai il lotto</p>
                        <select id="workCat"></select>
                    </div>
                    <div class="align-center-column-direction">
                        <p style="font-weight: bold">Inserisci un nome e una descrizione</p>
                        <input placeholder="Titolo" type="text" id="prodBatchName"style="margin-bottom: 10px">
                        <textarea placeholder="Descrizione" id="prodBatchDescription"></textarea>
                    </div>
                    <div class="align-center-column-direction">
                        <label><strong>Giorni di lavoro</strong></label>
                        <input type="number" id="deliveryTime" name="deliveryTime">
                    </div>
                    <div>
                        <p style="margin-top: 30px; font-weight: bold">------  FACOLTATIVO  ------</p>
                        <input id="mp" type="checkbox">
                        <label for="mp">Pubblicare sul Marketplace?</label>
                            <div style="display: block">
                                <input id="unitPrice" type="text" step="0.01" min="0">
                                <label for="unitPrice">Assegna un prezzo unitario</label>
                            </div>
                    </div>`
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            let mp = false;
            if($('#mp').is(':checked')){
                mp = true;
            }

            const dataDesc = {
                mp: mp,
                unitPrice: $('#unitPrice').val(),
                desc: $('#prodBatchDescription').val(),
                name: $('#prodBatchName').val(),
                workCat: $('#workCat').val(),
                deliveryTime: $("#deliveryTime").val()
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/EmptyProductBatchManage',
                data: dataDesc
            }).done(function (res) {
                bsModal.writeBody('Lotto creato con successo');
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    });


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

        if(selectedRowsCount < 1){
            new Alert({
                type: "warning",
                message: "Devi selezionare uno o più lotti in cui associare la fattura"
            }).open();
            return false;
        }

        var i = 0;
        var rows = [];
        $.each(selectedRows, function (k, v) {
            rows.push(v.row_id);
        });

        //Far scegliere
        modal = new $.bsModal(
            'Registrazione fattura',
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
                        'Totale fattura' +
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
                        '<label for="invoiceTotalPreview">Totale fattura</label>' +
                        '<input type="text" class="form-control" id="invoiceTotalPreview" name="invoiceTotalPreview" value="' + res.total +'" readonly/>' +
                        '</div>' +
                        '<div class="col-sm-6">' +
                        '<div class="form-group">' +
                        '<label for="invoiceTotal">Totale fattura da fason</label>' +
                        '<input style="" type="text" class="form-control inputPrice" id="invoiceTotal" name="invoiceTotal" value="' + res.total +'" />' +
                        '</div>' +
                        '</div>';
                    '</div>'
                    '</form>';

                    var body = '<h4>Riepilogo</h4>';
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

    $(document).on('bs.product.batch.to.fason', function () {

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1){
            new Alert({
                type: "warning",
                message: "Seleziona un lotto alla volta"
            }).open();
            return false;
        }

        let productBatchId = selectedRows[0].row_id;

        //SELEZIONA IL FOISON
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Foison'
            },
            dataType: 'json'
        }).done(function (res) {
            var select = $('#foison');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: ['name'],
                options: res
            });
        });

        let bsModal = new $.bsModal('Assegna un lotto', {
            body: '<p>Assegna un nuovo lotto</p>' +
            '<div class="form-group form-group-default required">' +
            '<label>Scegli il Foison</label>' +
            '<select class="full-width selectpicker"\n id="foison"' +
            'placeholder="Seleziona il Foison" tabindex="-1"\n' +
            'title="foison" name="foison" id="foison">\n' +
            '</select>'+
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label>Scegli il Contratto</label>' +
            '<select class="full-width selectpicker"\n id="contract"' +
            'placeholder="Seleziona il contratto" tabindex="-1"\n' +
            'title="contract" name="contract" id="contract">\n' +
            '</select>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label>Scegli i dettagli del contratto</label>' +
            '<select class="full-width selectpicker"\n id="contractDetails"' +
            'placeholder="Seleziona i dettagli del contratto" tabindex="-1"\n' +
            'title="contractDetails" name="contractDetails" id="contractDetails">\n' +
            '</select>' +
            '</div>' +
            '<div>' +
            '<p id="prodBatchValue">Valore</p>' +
            '<p id="prodSectional">Sezionale</p>' +
            '<button id="costWork" name="costWork">Anteprima costo e sezionale</button>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
            '<label>Data di Consegna</label>' +
            '<input type="date" id="deliveryDate" name="deliveryDate">' +
            '</div>'
        });

        //setto i contratti a seconda del foison
        $('#foison').change(function () {
            let foisonId = $('#foison').val();
            $.ajax({
                method:'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Contracts',
                    condition: {
                        foisonId: foisonId
                    },
                },
                dataType: 'json'
            }).done(function (res) {
                var select = $('#contract');
                if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    options: res
                });
            });
        });

        //setto i dettagli a seconda dei contratti
        $('#contract').change(function () {
            let contractId = $('#contract').val();
            $.ajax({
                method:'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'ContractDetails',
                    condition: {
                        contractId: contractId,
                        accepted: 1
                    }
                },
                dataType: 'json'
            }).done(function (res) {
                var select = $('#contractDetails');
                if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'contractDetailName',
                    options: res
                });
            });
        });


        $('#costWork').on('click', function () {
            if($('#contractDetails').val()){
                const data = {
                    contractDetail: $('#contractDetails').val(),
                    productBatchId: productBatchId
                };
                $.ajax({
                    method: 'get',
                    url: '/blueseal/xhr/AssociateEmptyProductBatchManage',
                    data: data
                }).done(function (res) {
                    res = JSON.parse(res);
                    $('#prodBatchValue').text(res.cost + 'Euro');
                    $('#prodSectional').text(res.sectional);
                }).fail(function (res) {
                    $('#prodBatchValue').text('Errore');
                });
            } else {
                $('#prodBatchValue').text('Completa i campi soprastanti');
            }

        });



        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                productBatchId: productBatchId,
                foisonId: $('#foison').val(),
                contractId: $('#contract').val(),
                contractDetailsId: $('#contractDetails').val(),
                deliveryDate: $('#deliveryDate').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/AssociateEmptyProductBatchManage',
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

    });


    $(document).on('bs.unfit.batch', function () {

        //Prendo tutti i lotti selezionati
        let productsBatch = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        $.each(selectedRows, function (k, v) {
            productsBatch.push({
                batch: v.row_id,
                fason: v.foisonEmail
            })
        });

        let bsModal = new $.bsModal('LOTTO NON IDONEO', {
            body: '<p>Inviare la notifica al Fason per "lotto non idoneo"?</p>'
        });


        const data = {
            pB: productsBatch
        };

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/UnfitProductBatchManage',
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

    $(document).on('bs.product.batch.valutation', function () {

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length != 1)
        {
            new Alert({
                type: "warning",
                message: "Valuta un lotto alla volta"
            }).open();
            return false;
        }

        let bsModal = new $.bsModal('Valutazione del lotto', {
            body: `<p>Inserisci un voto(puoi utilizzare numeri con la virgola)</p>
                   <input type="number" min="0" value="0" step="0.01" id="rank">`
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                pb: selectedRows[0].row_id,
                ranking: $('#rank').val(),
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/RankProductBatchAjaxController',
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