window.buttonSetup = {
    tag: "a",
    icon: "fa-window-close",
    permission: "/admin/product/edit&&allShops",
    event: "bs-delete-image-from-batch",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Elimina immagine dal lotto",
    placement: "bottom"
};

$(document).on('bs-delete-image-from-batch', function () {

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



    let bsModal = new $.bsModal('Elimina le immagini da un lotto', {
        body: `<p>Sicuro di voler eliminare questi immagini dal lotto?</p>`
    });

    let emptyBatch = (window.location.href.split('/').slice(-2)[0] === 'prodotti' ? 'empty' : 'full');



    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            products: selectedProduct,
            productBatchId: window.location.href.substring(window.location.href.lastIndexOf('/') + 1),
            emptyBatch: emptyBatch
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/ImageBatchManage',
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