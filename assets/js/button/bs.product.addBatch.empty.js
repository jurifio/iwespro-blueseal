window.buttonSetup = {
    tag:"a",
    icon:"fa-plus",
    permission:"/admin/product/edit&&allShops",
    event:"bs-new-batch-product-add-empty",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Associa prodotti a un lotto esistente",
    placement:"bottom",
    toggle:"modal"
};


    $(document).on('bs-new-batch-product-add-empty', function () {

        //Prendo tutti i prodotti selezionati
        let selectedProduct = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        //id-variantId in array
        $.each(selectedRows, function (k, v) {
            selectedProduct.push(v.DT_RowId);
        });

        let numberOfProduct = selectedProduct.length;

        if(numberOfProduct == 0){
            new Alert({
                type: "warning",
                message: "Devi selezionare almeno un prodotto"
            }).open();
            return false;
        }



        let bsModal = new $.bsModal('Associa prodotti a un lotto esistente', {
            body: `<p>Inserisci il numero di lotto a cui aggiungere i prodotti</p>
                    <input type="number" min="0" id="pb">`
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                batch: $('#pb').val(),
                products: selectedProduct
            };
            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/EmptyProductBatchManage',
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

});