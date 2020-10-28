window.buttonSetup = {
    tag: "a",
    icon: "fa-folder-open",
    permission: "/admin/product/edit&&allShops",
    event: "bs-explodeto-folder",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Crea Lotto Post Produzione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-explodeto-folder', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Seleziona un file alla Volta"
        }).open();
        return false;
    }

    let postId = selectedRows[0].row_id;
    let bsModal = new $.bsModal('Creazione Lotto ', {
        body: 'Vuoi Creare il lotto e pubblicarlo sul marketplace?'

    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].row_id;
        let fileName =selectedRows[0].row_fileName;
        let shopId= selectedRows[0].shopId;


        $.ajax({
            method: "post",
            url: "/blueseal/xhr/ExplodeZipAjaxController",
            data: {
                id: id,
                fileName : fileName,
                shopId : shopId
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                $.refreshDataTable();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});