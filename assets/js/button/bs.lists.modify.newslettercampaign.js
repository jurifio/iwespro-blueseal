window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newslettercampaign-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita la Campagna",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newslettercampaign-edit', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una distinta per Cambiare la data"
        }).open();
        return false;
    }

    let newsletterCampaignId = selectedRows[0].id;
    let bsModal = new $.bsModal('Invio', {
        body: '<p>Invia La Newsletter selezionata</p>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="campaignName">Nome Campagna</label>' +
        '<input autocomplete="on" type="text" id="campaignName" ' +
        'class="form-control" name="campaignName" value="' + selectedRows[0].name + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="dateCampaignStart">Data di Inizio Campagna</label>' +
        '<input autocomplete="off" type="datetime-local" id="dateCampaignStart"' +
        'class="form-control" name="dateCampaignStart" value="' + selectedRows[0].dateCampaignStart + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="dateCampaignFinish">Data di Fine  Campagna</label>' +
        '<input autocomplete="off" type="datetime-local" id="dateCampaignFinish"' +
        'class="form-control" name="dateCampaignFinish" value="' + selectedRows[0].dateCampaignFinish + '">' +
        '</div>' +
        '</div>'
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;
        let dateCampaignStart = $('#dateCampaignStart').val();
        let dateCampaignFinish = $('#dateCampaignFinish').val();
        let name = $('#campaignName').val();

        $.ajax({
            method: "put",
            url: "/blueseal/xhr/NewsletterCampaignManage",
            data: {
                id: id,
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
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });
});
