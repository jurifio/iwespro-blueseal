window.buttonSetup = {
    tag: "a",
    icon: "fa-thumbs-up",
    permission: "/admin/product/edit&&allShops",
    event: "bs-approve-post-tosocial",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Approva il Post",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-approve-post-tosocial', function (e, element, button) {
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
    let bsModal = new $.bsModal('Approva l\'Anteprima del Post', {
        body: 'Approvare il Post?'

    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].row_id;


        $.ajax({
            method: "post",
            url: "/blueseal/xhr/EditorialPlanDetailApproveAjaxController",
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