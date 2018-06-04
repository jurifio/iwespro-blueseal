window.buttonSetup = {
    tag: "a",
    icon: "fa-window-close",
    permission: "/admin/product/edit&&allShops",
    event: "bs-delete-product-brand-from-batch",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Elimina brand dal lotto",
    placement: "bottom"
};

$(document).on('bs-delete-product-brand-from-batch', function () {

    //Prendo tutti i prodotti selezionati
    let selectedProductBrand = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    //id-variantId in array
    $.each(selectedRows, function (k, v) {
        selectedProductBrand.push(v.DT_RowId);
    });

    let numberOfProduct = selectedProductBrand.length;

    if(numberOfProduct == 0){
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }



    let bsModal = new $.bsModal('Elimina i brand da un lotto', {
        body: `<p>Sicuro di voler eliminare questi prodotti dal lotto?</p>`
    });

    let emptyBatch = (window.location.href.split('/').slice(-2)[0] === 'brands' ? 'empty' : 'full');



    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            brands: selectedProductBrand,
            productBatchId: window.location.href.substring(window.location.href.lastIndexOf('/') + 1),
            emptyBatch: emptyBatch
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/EmptyProductBrandBatchManage',
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