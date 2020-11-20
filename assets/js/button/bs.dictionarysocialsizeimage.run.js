window.buttonSetup = {
    tag: "a",
    icon: "fa-rocket",
    permission: "/admin/product/list&&allShops",
    event: "bs-dictionarysocialsizeimage-run",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Remasterizzazione Foto Social",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-dictionarysocialsizeimage-run', function () {
    let bsModal = new $.bsModal('Remasterizzazione Foto Social', {
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
            url: "/blueseal/xhr/DictionaryRemasterSocialImageSizeAjaxController",
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