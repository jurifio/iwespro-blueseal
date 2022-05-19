window.buttonSetup = {
    tag: "a",
    icon: "fa-download",
    permission: "/admin/product/edit&&allShops",
    event: "bs-getfattureincloud-list",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Remasterizzazione Foto",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-getfattureincloud-list', function () {
    let bsModal = new $.bsModal('Scarica Fatture Friends', {
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
            url: "/blueseal/xhr/GetFattureinCloudInvoiceListAjaxController",
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