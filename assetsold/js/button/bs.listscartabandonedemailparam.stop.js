window.buttonSetup = {
    tag: "a",
    icon: "fa-stop",
    permission: "/admin/product/edit&&allShops",
    event: "bs-listscartabandonedemailparam-stop",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Ferma il Job",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-listscartabandonedemailparam-stop', function () {



    let bsModal = new $.bsModal('Ferma il Job ', {
        body: '<div><p>Conferma'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
          stop:"1"

        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/CartAbandonedStopJobAjaxController',
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