window.buttonSetup = {
    tag: "a",
    icon: "fa-upload",
    permission: "/admin/product/edit&&allShops",
    event: "bs-listcategory-create",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Crea i file di esportazione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-listcategory-create', function () {
    let bsModal = new $.bsModal('Crea i file di esportazione', {
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
            url: "/blueseal/xhr/PrestashopDumpCsv",
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