window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/product/edit&&allShops",
    event: "bs-listscartabandonedemailparam-delete",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella Regola",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-listscartabandonedemailparam-delete', function () {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare la Regola per Cancellarla"
        }).open();
        return false;
    }
    let id = selectedRows[0].id;


    $.ajax({
        method: "put",
        url: "/blueseal/xhr/CartAbandonedEmailParamDelete",
        data: {
            id: id
        }
    }).done(function (res) {
        bsModal.writeBody(res);
    }).fail(function (res) {
        bsModal.writeBody(res);
    }).always(function (res) {
        bsModal.setOkEvent(function () {
            window.location.reload();
            bsModal.hide();
            // window.location.reload();
        });
        bsModal.showOkBtn();
    });
});