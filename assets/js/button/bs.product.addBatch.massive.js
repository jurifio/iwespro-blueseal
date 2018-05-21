window.buttonSetup = {
    tag:"a",
    icon:"fa-plus-circle",
    permission:"/admin/product/edit&&allShops",
    event:"bs-new-batch-product-add-massive",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Associa prodotti a un lotto esistente",
    placement:"bottom",
    toggle:"modal"
};


    $(document).on('bs-new-batch-product-add-massive', function () {

        let bsModal = new $.bsModal('Associa prodotti a un lotto esistente', {
            body: ` <p>N. lotto</p>
                    <input type="number" min="0" id="pBatch">
                    <p>Inserisci i codici</p>
                    <textarea id="codePr" style="width: 70%; height: 150px;"></textarea>`
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                batch: $('#pBatch').val(),
                products: $('#codePr').val(),
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/MassiveProductBatchManage',
                data: data
            }).done(function (res) {

               let arr = JSON.parse(res);
               let ris = '';

                if (typeof(arr['ko']) != 'undefined' && arr['ko'] != null ) {
                    $.each(arr['ko'], function (k, v) {
                        $.each(v, function (k, v) {
                            ris += `Mancante: ${k} <br>`;
                        });
                        ris += `<strong>Prodotto: ${k}</strong> <br>`;
                    });
                }

                bsModal.writeBody(ris);
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