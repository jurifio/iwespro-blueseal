window.buttonSetup = {
    tag: "a",
    icon: "fa-volume-off",
    permission: "/admin/product/edit&&allShops",
    event: "bs-dictionaryvideo-run",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Togliere Audio su  Video",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-dictionaryvideo-run', function () {
    let bsModal = new $.bsModal('Remasterizzazione Video ', {
        body: '<div><p>Togliere Audio  '+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
const data={
    id: '1',
        };
        $.ajax({
            method: 'POST',
            url: "/blueseal/xhr/DictionaryRemasterVideoAjaxController",
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