window.buttonSetup = {
    tag: "a",
    icon: "fa-play",
    permission: "/admin/product/edit&&allShops",
    event: "bs-listscartabandonedemailparam-start",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Avvia il Job",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-listscartabandonedemailparam-start', function () {



    let bsModal = new $.bsModal('Avvia il Job ', {
        body: '<div><p>Conferma'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
          start:"1"

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/CartAbandonedStartJobAjaxController',
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