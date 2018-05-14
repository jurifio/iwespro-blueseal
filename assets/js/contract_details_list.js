;(function () {

    $(document).on('bs-contract-detail-add', function () {
        //SELECT PER SELEZIONARE LA CATEGORIA
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'WorkCategory'
            },
            dataType: 'json'
        }).done(function (res) {
            var select = $('#workCategory');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                options: res
            });
        });

        let bsModal = new $.bsModal('Crea dettaglio del contratto', {
            body: '<p>Inserisci un nuovo contratto</p>' +
            '<div class="form-group form-group-default required">' +
                '<select class="full-width selectpicker"\n id="workCategory"' +
                'placeholder="Seleziona la categoria" tabindex="-1"\n' +
                'title="workCategory" name="workCategory" id="workCategory">\n' +
                '</select>'+
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<select class="full-width selectpicker"\n id="workPriceList"' +
                'placeholder="Seleziona il listino corretto" tabindex="-1"\n' +
                'title="workPriceList" name="workPriceList" id="workPriceList">\n' +
                '</select>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label for="contractDetailName">Nome del contratto</label>' +
                '<input type="text" id="contractDetailName" name="contractDetailName">' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label for="qty">Quantità giornaliera</label>' +
                '<input type="text" id="qty" name="qty">' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label for="note">Note</label>' +
                '<textarea id="note" name="note"></textarea>' +
            '</div>'
        });

        $('#workCategory').change(function () {
            let workCategoryId = $('#workCategory').val();
            $.ajax({
                method:'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'WorkPriceList',
                    condition: {
                        workCategoryId: workCategoryId
                    },
                },
                dataType: 'json'
            }).done(function (res) {
                var select = $('#workPriceList');
                if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    options: res
                });
            });
        });



        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            let url = window.location.href;
            let contractId = url.substring(url.lastIndexOf('/') + 1);
            const data = {
                workCategoryId: $('#workCategory').val(),
                workPriceListId: $('#workPriceList').val(),
                contractId: contractId,
                contractDetailName: $('#contractDetailName').val(),
                qty: $('#qty').val(),
                note: $('#note').val()
            };
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/ContractDetailsListManage',
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

    $(document).on('bs-contract-detail-accept', function () {

        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length === 1){


            let contractDetailId = selectedRows[0].id;
            let contractDetailName = selectedRows[0].contractName;

            let bsModal = new $.bsModal('Accettazione del contratto', {
                body: '<div class="form-group form-group-default required">' +
                '<p>Confermi di voler accettare le condizioni del contratto con:' +
                '<br />' + 'Codice: ' +
                '<strong>' + contractDetailId + '</strong>' +
                '<br />' + 'Nome: ' +
                '<strong>' + contractDetailName + '</strong>' +
                '</p>'+
                '</div>'
            });

            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                   contractDetailId: contractDetailId
                };
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/ContractAcceptedManage',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        $.refreshDataTable();
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
            });
        } else if (selectedRows.length < 1) {
            new Alert({
                type: "warning",
                message: "Non hai selezionato nessun contratto"
            }).open();
            return false;
        } else if (selectedRows.length > 1) {
            new Alert({
                type: "warning",
                message: "Puoi accettare un solo contratto alla volta"
            }).open();
            return false;
        }

    });
})();