window.buttonSetup = {
    tag: "a",
    icon: "fa-check",
    permission: "/admin/product/edit&&allShops",
    event: "bs-dictionarysizerenameimage-run",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Rinomina Foto",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-dictionarysizerenameimage-run', function () {
    let bsModal = new $.bsModal('Rinomina Foto', {
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
            url: "/blueseal/xhr/DictionaryRemasterImageRenameAjaxController",
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