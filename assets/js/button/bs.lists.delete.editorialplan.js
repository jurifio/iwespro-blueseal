window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/product/delete&&allShops",
    event: "bs-editorialplan-delete",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella il Piano Editoriale",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-editorialplan-delete', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una newsletter  per eliminarla"
        }).open();
        return false;
    }

    let editorialPlanId = selectedRows[0].id;
    let bsModal = new $.bsModal('Cancellazione', {
        body: '<p>Cancella Il Piano Editoriale Selezionato0</p>'
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;


        $.ajax({
            method: "put",
            url: "/blueseal/xhr/EditorialPlanDelete",
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