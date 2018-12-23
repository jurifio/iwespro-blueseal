window.buttonSetup = {
    tag: "a",
    icon: "fa-image",
    permission: "/admin/product/edit&&allShops",
    event: "bs-image-update",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiornamento Immagini ",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-image-update', function () {
    let bsModal = new $.bsModal('Aggiornamento Immagini Prodotti', {
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
            url: "/blueseal/xhr/PrestashopAlignImage",
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