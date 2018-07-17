window.buttonSetup = {
    tag: "a",
    icon: "fa-rocket",
    permission: "/admin/product/edit&&allShops",
    event: "bs-listscartabandonedemailsend-generate",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Esporta in CSV",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-listscartabandonedemailsend-generate', function () {
    let bsModal = new $.bsModal('Salva La Pianificazione', {
        body: '<div><p>Conferma'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        const data = {

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/CartAbandonedListAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});