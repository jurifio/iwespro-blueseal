;(function () {
    $(document).on('bs.end.product.modify', function () {

        //Prendo tutti i prodotti selezionati
        let selectedProductBatchDetailIds = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        //id-variantId in array
        $.each(selectedRows, function (k, v) {
            selectedProductBatchDetailIds.push(v.id);
        });

        let bsModal = new $.bsModal('Conferma Normalizzazione Prodotti', {
            body: '<p>Confermi la fine della procedura di normalizzazione per i prodotti selezionati?</p>'
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                ids: selectedProductBatchDetailIds
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ProductBatchDetailsManage',
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


    $(document).on('bs.end.product.modify.notify', function () {

        let bsModal = new $.bsModal('Notifica fine normalizzazione', {
            body: '<p>Notificare via mail la fine della normalizzazione dei prodotti?</p>'
        });

        let url = window.location.href;
        let productBatchId = url.substring(url.lastIndexOf('/') + 1);

        const data = {
            productBatchId: productBatchId
        };

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/ProductBatchDetailsManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });

    });
})();