window.buttonSetup = {
    tag: "a",
    icon: "fa-upload",
    permission: "/admin/product/edit&&allShops",
    event: "bs-order-remote-pickup",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Importa gli ordini Esterni",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-order-remote-pickup', function () {
    let bsModal = new $.bsModal('Importa gli Ordini', {
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
            url: "/blueseal/xhr/ImportExternalOrder",
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