window.buttonSetup = {
    tag: "a",
    icon: "fa-check-square",
    permission: "/admin/product/edit&&allShops",
    event: "bs-dictionarysizerenametometaimage-run",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Rinomina Foto",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-dictionarysizerenametometaimage-run', function () {
    let bsModal = new $.bsModal('Rinomina Foto standard', {
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
            url: "/blueseal/xhr/DictionaryRemasterImageRenameToMetaAjaxController",
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