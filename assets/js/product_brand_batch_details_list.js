;(function () {

    $(document).on('bs.end.work.product.brand', function () {

        let selectedProductBrands = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        //id-variantId in array
        $.each(selectedRows, function (k, v) {
            selectedProductBrands.push(v.DT_RowId);
        });

        let bsModal = new $.bsModal('Conferma Normalizzazione Prodotti', {
            body: '<p>Confermi la fine della procedura di normalizzazione per i prodotti selezionati?</p>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                ids: selectedProductBrands,
                batch: window.location.href.substring(window.location.href.lastIndexOf('/') + 1)
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/EmptyProductBrandBatchManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

    });

})();