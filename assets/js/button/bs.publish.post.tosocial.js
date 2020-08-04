window.buttonSetup = {
    tag: "a",
    icon: "fa-share-square-o",
    permission: "/admin/product/edit&&allShops",
    event: "bs-publish-post-tosocial",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Pubblica sui Social",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-publish-post-tosocial', function (e, element, button) {
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
    let bsModal = new $.bsModal('Pubblica il Post', {
        body: '<p>Hai Deciso di Pubblicare il Post ' + selectedRows[0].title +'</p>'

    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].row_id;
        let dateCampaignStart = $('#dateCampaignStart').val();
        let dateCampaignFinish = $('#dateCampaignFinish').val();
        let name = $('#campaignName').val();

        $.ajax({
            method: "post",
            url: "/blueseal/xhr/EditorialPlanDetailPublishAjaxController",
            data: {
                editorialPlanDetailId: id,
                dateCampaignStart: dateCampaignStart,
                dateCampaignFinish: dateCampaignFinish,
                name: name,
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