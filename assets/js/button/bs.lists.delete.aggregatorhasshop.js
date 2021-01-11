window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/product/delete&&allShops",
    event: "bs-lists-delete-aggregatorhasshop",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella l'account aggregatore",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-lists-delete-aggregatorhasshop', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un associazione"
        }).open();
        return false;
    }

    let marketplaceHasShopId = selectedRows[0].id;
    let bsModal = new $.bsModal('Cancellazione', {
        body: '<p>Associazione Aggregatore Da Cancellare Confermi?</p>'
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].DT_RowId;


        $.ajax({
            method: "delete",
            url: "/blueseal/xhr/AggregatorAccountShopInsert",
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
});