window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/product/delete&&allShops",
    event: "bs-lists-delete-productsharehasnewshopdestination",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella la regola dei Prodotti Parallei ",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-lists-delete-productsharehasnewshopdestination', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una Regola"
        }).open();
        return false;
    }

    let newsletterEventId = selectedRows[0].id;
    let bsModal = new $.bsModal('Cancellazione', {
        body: '<p>Regola Prodotti Paralleli Cancellata</p>'
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].DT_RowId;


        $.ajax({
            method: "delete",
            url: "/blueseal/xhr/MarketplaceAccountHasShopInsertManage",
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