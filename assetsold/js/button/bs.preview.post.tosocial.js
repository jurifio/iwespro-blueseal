window.buttonSetup = {
    tag: "a",
    icon: "fa-eye",
    permission: "/admin/product/edit&&allShops",
    event: "bs-preview-post-tosocial",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Visualizza Anteprima",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-preview-post-tosocial', function (e, element, button) {
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
    let bsModal = new $.bsModal('Visualizza l\'Anteprima del Post', {
        body: '<p>il creative Id e\' ' + selectedRows[0].creativeId +'</p>'

    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].row_id;
        let dateCampaignStart = $('#dateCampaignStart').val();
        let dateCampaignFinish = $('#dateCampaignFinish').val();
        let name = $('#campaignName').val();

        $.ajax({
            method: "get",
            url: "/blueseal/xhr/EditorialPlanDetailPreviewAjaxController",
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