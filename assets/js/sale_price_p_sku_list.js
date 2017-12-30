;(function () {

    $(document).on('bs-sale-price-modify', function () {

        let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();
        let ids = selectedRows[0].id.split('-');
        var productId = ids[0];
        var productVariantId = ids[1];

        if (selectedRows.length === 1) {

            let bsModal = new $.bsModal('Aggiorna i prezzi in saldo', {
                body: '<p>Inserisci il nuovo prezzo scontato</p>' +
                '<div class="form-group form-group-default required">' +
                '<label for="changePrice">Metti il nuovo prezzo totale (Product SKU)</label>' +
                '<input autocomplete="off" type="text" id="changePrice" ' +
                'placeholder="Nuovo prezzo totale" class="form-control" name="changePrice" required="required">' +
                '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="changeSalePrice">Metti il nuovo prezzo in saldo (Product SKU)</label>' +
                '<input autocomplete="off" type="text" id="changeSalePrice" ' +
                'placeholder="Nuovo prezzo in saldo" class="form-control" name="changeSalePrice" required="required">' +
                '</div>'
            });

            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    productId:  productId,
                    productVariantId:  productVariantId,
                    newPrice: $('input#changePrice').val(),
                    newSalePrice: $('input#changeSalePrice').val()
                };
                $.ajax({
                    method: 'put',
                    url: '/blueseal/xhr/SalePriceProductSkuModify',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        //refresha solo tabella e non intera pagina
                        $.refreshDataTable();
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
            });
        } else if(selectedRows.length < 1){
            new Alert({
                type: "warning",
                message: "Non hai selelezionato nessuna riga"
            }).open();
        } else if (selectedRows.length > 1){
            new Alert({
                type: "warning",
                message: "Seleziona una sola riga"
            }).open();
        }
    });

})();