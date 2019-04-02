window.buttonSetup = {
    tag: "a",
    icon: "fa-rocket",
    permission: "/admin/product/edit&&allShops",
    event: "bs-dictionarysizeimage-run",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Remasterizzazione Foto",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-dictionarysizeimage-run', function () {
    let bsModal = new $.bsModal('Remasterizzazione Foto', {
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
            url: "/blueseal/xhr/DictionaryRemasterImageSizeAjaxController",
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