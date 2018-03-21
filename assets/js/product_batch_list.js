;(function () {

    $(document).on('bs.end.product.batch', function () {

        //Prendo tutti i lotti selezionati
        let productsBatch = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        $.each(selectedRows, function (k, v) {
            productsBatch.push(v.row_id);
        });

        let bsModal = new $.bsModal('Chiudi lotto', {
            body: '<p>Chiudere il lotto in via definitiva?</p>'
        });


        const data = {
            productBatchIds: productsBatch
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

})();