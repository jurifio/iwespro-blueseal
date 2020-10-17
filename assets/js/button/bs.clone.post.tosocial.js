window.buttonSetup = {
    tag: "a",
    icon: "fa-clone",
    permission: "/admin/product/edit&&allShops",
    event: "bs-clone-post-tosocial",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Vuoi Clonare il Post",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-clone-post-tosocial', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Seleziona un Post alla Volta"
        }).open();
        return false;
    }

    let postId = selectedRows[0].row_id;
    let bsModal = new $.bsModal('Clona  il  Post', {
        body: `<div class="row"><div class="col-md-12">Vuoi Clonare il Post?</div></div>
               `

    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].row_id;


        $.ajax({
            method: "put",
            url: "/blueseal/xhr/EditorialPlanDetailCloneAjaxController",
            data: {
                editorialPlanDetailId: id

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