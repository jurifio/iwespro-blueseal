window.buttonSetup = {
    tag: "a",
    icon: "fa-plus",
    permission: "/admin/product/edit&&allShops",
    event: "bs-insert-productprestashop",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Inserimento Nuovo Prodotto su MarketPlace",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-insert-productprestashop', function () {
    let bsModal = new $.bsModal('Inserimento Nuovo Prodotto su MarketPlace', {
        body: '<div><p>Conferma'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
const data={
    id: '1',
        };
        $.ajax({
            method: 'POST',
            url: "/blueseal/xhr/PrestashopInsertNewProduct",
            data:data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
               // window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });

    });
});