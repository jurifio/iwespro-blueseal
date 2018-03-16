window.buttonSetup = {
    tag:"a",
    icon:"fa-file-word-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-new-batch-product-add",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Crea lotto",
    placement:"bottom",
    toggle:"modal"
};


    $(document).on('bs-new-batch-product-add', function () {

        //Prendo tutti i prodotti selezionati
        let selectedProduct = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        //id-variantId in array
        $.each(selectedRows, function (k, v) {
            selectedProduct.push(v.DT_RowId);
        });

        let numberOfProduct = selectedProduct.length;

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

        let bsModal = new $.bsModal('Assegna un utente', {
            body: '<p>Inserisci un nuovo contratto</p>' +
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
                '<button id="costWork" name="costWork">Prevedi costo</button>' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label>Data di Consegna</label>' +
                '<input type="date" id="deliveryDate" name="deliveryDate">' +
            '</div>' +
            '<div class="form-group form-group-default required">' +
                '<label>Data di Chiusura</label>' +
            '   <input type="date" id="closingDate" name="closingDate">' +
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
                        contractId: contractId
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
                    numberOfProduct: numberOfProduct
                };
                $.ajax({
                    method: 'get',
                    url: '/blueseal/xhr/ProductBatchManage',
                    data: data
                }).done(function (res) {
                    $('#prodBatchValue').text(res + 'Euro');
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
                products: selectedProduct,
                foison: $('#foison').val(),
                contract: $('#contract'),
                contractDetails: $('#contractDetails'),
                deliveryDate: $('#deliveryDate'),
                closingDate: $('#closingDate')
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