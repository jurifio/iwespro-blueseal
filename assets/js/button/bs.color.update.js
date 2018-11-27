window.buttonSetup = {
    tag: "a",
    icon: "fa-paint-brush",
    permission: "/admin/product/edit&&allShops",
    event: "bs-color-update",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiornamento Colore Prodotti-Varianti",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-color-update', function () {
    let bsModal = new $.bsModal('Aggiornamento Colori prodotti', {
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
            url: "/blueseal/xhr/PrestashopAlignColorProduct",
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