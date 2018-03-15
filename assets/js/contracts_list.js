;(function () {

    $(document).on('bs-contract-add', function () {

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Foison'
            },
            dataType: 'json'
        }).done(function (res) {
            var select = $('#foisonSelect');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                options: res
            });
        });

        let bsModal = new $.bsModal('Assegna un utente', {
            body: '<p>Inserisci un nuovo contratto</p>' +
            '<div class="form-group form-group-default required">' +
                '<select class="full-width selectpicker"\n id="foisonSelect"' +
                'placeholder="Seleziona il Foison" tabindex="-1"\n' +
                'title="foisonSelect" name="foisonSelect" id="foisonSelect">\n' +
                '</select>' +
            '</div>'+
            '<div class="form-group form-group-default required">' +
                '<label for="contractName">Titolo contratto</label>' +
                '<input autocomplete="off" type="text" id="contractName" ' +
            'placeholder="Titolo del Contratto" class="form-control" name="contractName" required="required">' +
            '</div>'+
            '<div class="form-group form-group-default required">' +
                '<label for="contractDescription">Descrizione contratto</label>' +
                '<textarea id="contractDescription"></textarea>' +
            '</div>'
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
})();