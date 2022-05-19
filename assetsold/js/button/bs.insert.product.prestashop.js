window.buttonSetup = {
    tag:"a",
    icon:"fa-sun-o",
    permission:"/admin/product/edit&&allShops",
    event:"bs-insert-product-prestashop",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Inserisci il prodotto nella lista dedicata a Prestashop",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-insert-product-prestashop', function () {

    let bsModal = new $.bsModal("Prestashop", {
        body: `<p>Inserire i prodotti selezionati nella lista dedicata a Prestashop?</p>`
    });

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    let products = [];

    $.each(selectedRows, function (k, v) {
        products.push(v.DT_RowId);
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

            const data = {
                products: products
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ManageProductForPrestashopListAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody('Inserimento completato con successo');
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