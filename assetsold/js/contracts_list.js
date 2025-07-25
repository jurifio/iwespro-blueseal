;(function () {


    $(document).on('bs-contract-add', function () {


        let bsModal = new $.bsModal('Crea contratto', {
            body: `<div><p>Inserisci un nuovo contratto</p>
                <p>Con questa operazione si definisce l\'arco temporale in cui è valido il contratto. All\'interno di esso verranno in seguito definiti tutti i dettagli.</p>
                </div>
                <div class="form-group form-group-default required">
                <label for="contractName">Titolo contratto</label>
                <input autocomplete="off" type="text" id="contractName" 
                placeholder="Titolo del Contratto" class="form-control" name="contractName" required="required">
                </div>
                <div class="form-group form-group-default required">
                <label for="contractDescription">Descrizione contratto</label>
                <textarea id="contractDescription"></textarea>
                </div>
                <div class="form-group form-group-default selectize-enabled">
                <label for="foisonSelect">Seleziona Operator</label>
                <select id="foisonSelect" name="foisonSelect"
                class="full-width selectpicker"
                placeholder="Seleziona la Lista"
                data-init-plugin="selectize">
                </select>
                </div>`
        });
        Pace.ignore(function () {
            var foisonSelect = $('select[name=\"foisonSelect\"]');
            $.ajax({
                url: '/blueseal/xhr/SelectAllFoisonAjaxController',
                method: 'get',
                dataType: 'json'
            }).done(function (res) {
                console.log(res);
                foisonSelect.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: res,
                    render: {
                        item: function (item, escape) {
                            return '<div>' +
                                '<span class="label">' + escape(item.id) + '</span> - ' +
                                '<span class="caption">' + escape(item.name) + '</span>' +
                                '</div>'
                        },
                        option: function (item, escape) {
                            return '<div>' +
                                '<span class="label">' + escape(item.id) + '</span>  - ' +
                                '<span class="caption">' + escape(item.name) + '</span>' +
                                '</div>'
                        }
                    }
                });
            });
        });





        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                foisonId: $('#foisonSelect').val(),
                nContract: $('input#contractName').val(),
                dContract: $('#contractDescription').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ContractsManage',
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


    $(document).on('bs-contract-details-add', function () {
        let i = 0;
        let index = [];


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'WorkCategory'
            },
            dataType: 'json'
        }).done(function (cat) {
            var select = $('#workCategory');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: ['name'],
                options: cat
            });
        });


        let bsModal = new $.bsModal('Aggiungi listini', {
            body: `
        <div id="workPriceList">
        <div style="margin-bottom: 20px">
        <strong>SELEZIONA LA CATEGORIA</strong>    
        <select id="workCategory"></select>
        </div>
        <strong>INSERISCI UN NUOVO LISTINO</strong>
            <div style="margin-bottom: 10px" id="addRow">
            </div>
            <button id="addPriceLevel">AGGIUNGI LISTINO</button>
        </div>
        `
        });


        //aggiungo prezzi
        $('#addPriceLevel')
            .on('click', function () {
                i++;
                index.push(i);

                $('#addRow')
                    .append(
                        `<div id="formDetail-${i}" style="margin-bottom: 15px">
                        <strong class="col-md-12">Listino livello ${i}</strong>
                        <input placeholder="Nome Listino" type="text" id="name-${i}" name="name-${i}" class="col-md-6">
                        <input placeholder="Prezzo" type="text" id="price-${i}" name="price-${i}" class="col-md-6">
                        <input placeholder="Data inizio" type="datetime-local" id="start-${i}" name="start-${i}" class="col-md-6">
                        <input placeholder="Data fine" type="datetime-local" id="end-${i}" name="end-${i}" class="col-md-6">
                        <button id="removeDetail" data-id="${i}" style="margin-top: 10px">Rimuovi dettaglio</button>
                    </div>`
                    );
            });

        $(document)
            .on('click', '#removeDetail', function () {
                let id = $(this).attr('data-id');
                $(`#formDetail-${id}`).remove();

                let z = index.indexOf(parseInt(id));
                if (z !== -1) {
                    index.splice(z, 1);
                }
            });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let wcp = [];
            $.each(index, function (k, v) {

                wcp.push({
                    name: $(`#name-${v}`).val(),
                    price: $(`#price-${v}`).val(),
                    start: $(`#start-${v}`).val(),
                    end: $(`#end-${v}`).val()
                });

            });

            const data = {
                cat: $('#workCategory').val(),
                wcp: wcp
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/WorkPriceListManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
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
    $(document).on('bs-contract-close', function () {

        let selectedRows = $('.dataTable').DataTable().rows('.selected').data();

        if (selectedRows.count() != 1) {
            new Alert({
                type: "warning",
                message: "Chiudi un contratto alla volta!"
            }).open();
            return false;
        }

        let id = selectedRows[0].row_id;

        let bsModal = new $.bsModal('Chiudi contratto', {
            body: `Desideri chiudere definitivamente il contratto?`
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                contractId: id,
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ContractsManage',
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


    $(document).on('bs-contract-accept', function () {

        let selectedRows = $('.dataTable').DataTable().rows('.selected').data();

        if (selectedRows.count() != 1) {
            new Alert({
                type: "warning",
                message: "Firma un contratto alla volta!"
            }).open();
            return false;
        }

        let id = selectedRows[0].row_id;

        let bsModal = new $.bsModal('Firma il contratto', {
            body: `<div id="contract">
                      <div class="pre-scrollable" id="textC"></div>
                   </div>`
        });

        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/SingleContractManage',
            data: {
                idC: id
            }
        }).done(function (res) {
            $('#textC').append('<p>' + res + '</p>')
        });

        bsModal.addClass('modal-wide');
        bsModal.addClass('modal-high');

        bsModal.showCancelBtn();
        bsModal.setOkLabel('Accetta e stampa');
        bsModal.setOkEvent(function () {


            const data = {
                idC: id,
                textContract: $('#textC').html()
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/SingleContractManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody('Documento generato con successo');
                window.open(`/blueseal/download-contracts/${res}?type=Contracts`, '_blank');
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
})();



