window.buttonSetup = {
    tag: "a",
    icon: "fa-plus",
    permission: "/admin/product/edit&&allShops",
    event: "bs-listscartabandonedemailparam-add",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi Regola",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-listscartabandonedemailparam-add', function () {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.data().count();

  /*  if (selectedRows == 1) {
        new Alert({
            type: "warning",
            message: "Regola Già Esistente Non Puoi Aggiungere una nuova"
        }).open();
        return false;
    }*/
    let bsModal = new $.bsModal('Aggiungi Regola', {
        body: '<div><p>Conferma'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

var url="/blueseal/cartabandoned/add-plan";
window.location.href=url;

    });
});