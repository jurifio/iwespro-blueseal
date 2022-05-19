window.buttonSetup = {
    tag: "a",
    icon: "fa-upload",
    permission: "/admin/product/edit&&allShops",
    event: "bs-listcategory-update",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Testa la connessione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-listcategory-update', function () {
    let bsModal = new $.bsModal('check Immediato', {
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
            url: "/blueseal/xhr/PrestashopDumpProductImageCombination",
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