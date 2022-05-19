window.buttonSetup = {
    tag: "a",
    icon: "fa-exchange",
    permission: "/admin/product/edit&&allShops",
    event: "bs-quantity-update",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiornamento Quantità Prodotti e Varianti",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-quantity-update', function () {
    let bsModal = new $.bsModal('Aggiornamento Quantita Stock', {
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
            url: "/blueseal/xhr/PrestashopAlignQuantity",
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