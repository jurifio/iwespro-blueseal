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

        let bsModal = new $.bsModal('Assegna un utente', {
            body: '<p>Inserisci un nuovo contratto</p>' +
            '<div class="form-group form-group-default required">' +
                '<select class="full-width selectpicker"\n id="workCategory"' +
                'placeholder="Seleziona la categoria" tabindex="-1"\n' +
                'title="workCategory" name="workCategory" id="workCategory">\n' +
                '</select>'+
            '<div class="form-group form-group-default required">' +
                '<select class="full-width selectpicker"\n id="workPriceList"' +
                'placeholder="Seleziona il listino corretto" tabindex="-1"\n' +
                'title="workPriceList" name="workPriceList" id="workPriceList">\n' +
            '</select>'
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
                contractId: contractId
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
})();