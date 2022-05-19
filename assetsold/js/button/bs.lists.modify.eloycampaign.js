window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-eloycampaign-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita la Campagna",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-eloycampaign-edit', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una distinta per Cambiare la data"
        }).open();
        return false;
    }

    let eloyCampaignId = selectedRows[0].id;
    let bsModal = new $.bsModal('Invio', {
        body: '<p>Modifica La Newsletter selezionata</p>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="name">Nome Campagna</label>' +
        '<input autocomplete="on" type="text" id="campaignName" ' +
        'class="form-control" name="campaignName" value="' + selectedRows[0].name + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="startDate">Data di Inizio Campagna</label>' +
        '<input autocomplete="off" type="date" id="dateCampaignStart"' +
        'class="form-control" name="dateCampaignStart" value="' + selectedRows[0].startDate + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="dateCampaignFinish">Data di Fine  Campagna</label>' +
        '<input autocomplete="off" type="date" id="dateCampaignFinish"' +
        'class="form-control" name="dateCampaignFinish" value="' + selectedRows[0].endDate + '">' +
        '</div>' +
        '</div>'+
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="emailCampaign">Email per Invio </label>' +
        '<input autocomplete="on" type="text" id="emailCampaign" ' +
        'class="form-control" name="emailCampaign" value="' + selectedRows[0].emailCampaign + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="dateCampaignSend">Data di invio</label>' +
        '<input autocomplete="off" type="datetime-local" id="dateCampaignSend"' +
        'class="form-control" name="dateCampaignSend" value="' + selectedRows[0].sendDate + '">' +
        '</div>' +
        '</div>'
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;
        let dateCampaignStart = $('#dateCampaignStart').val();
        let dateCampaignFinish = $('#dateCampaignFinish').val();
        let name = $('#campaignName').val();
        let emailCampaign = $('#emailCampaign').val();
        let sendDate = $('#sendDate').val();

        $.ajax({
            method: "put",
            url: "/blueseal/xhr/NewsletterCampaignManage",
            data: {
                id: id,
                dateCampaignStart: dateCampaignStart,
                dateCampaignFinish: dateCampaignFinish,
                name: name,
                email: emailCampaign,

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
