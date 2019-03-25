;(function () {
    $(document).on('bs.add.presta.product', function () {

        //Prendo tutti i lotti selezionati
        let products = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length == 0){
            new Alert({
                type: "warning",
                message: "Seleziona almeno un prodotto"
            }).open();
            return false;
        }

        $.each(selectedRows, function (k, v) {
            products.push(v.productCode);
        });

        let bsModal = new $.bsModal('Inserisci prodotti all\'interno di un marketplace', {
            body: `
                <p>Seleziona un marketplace</p>
                <select id="selectMarketplace"></select>
            `
        });



        $.ajax({
            url: '/blueseal/xhr/PrestashopHasProductManage',
            method: 'GET',
            dataType: 'json'
        }).done(function(res){
            let select = $('#selectMarketplace');
            if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'shop-marketplace',
                options: res
            });
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                products: products,
                marketplaceHasShopId: $('#selectMarketplace').val()
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/PrestashopHasProductManage',
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