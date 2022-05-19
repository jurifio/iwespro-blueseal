window.buttonSetup = {
    tag: "a",
    icon: "fa-sticky-note",
    permission: "/admin/product/edit&&allShops",
    event: "bs-note-product-batch",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Inserisci un nuovo nome prodotto",
    placement: "bottom"
};

$(document).on('bs-note-product-batch', function () {

    //Prendo tutti i prodotti selezionati
    let selectedProduct = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    //id-variantId in array
    $.each(selectedRows, function (k, v) {
        selectedProduct.push(v.id);
    });

    if(selectedProduct.length == 0){
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }


    let bsModal = new $.bsModal('Aggiungi una nota per i prodotti selezionati', {
        body: `<p>Inserisci/accoda/sovrascrivi una nuova nota</p>
               <select style="display: block; margin: 0 auto" id="type">
               <option value="s">Inserisci/sovrascrivi</option>
               <option value="a">Accoda</option>
               </select>
               <textarea style="width: 400px; height: 400px;" id="newProductNote"></textarea>`
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {
            posProds: selectedProduct,
            note: $('#newProductNote').val(),
            type: $('#type').val()
        };
        $.ajax({
            method: 'put',
            url: '/blueseal/xhr/MassiveProductBatchManage',
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