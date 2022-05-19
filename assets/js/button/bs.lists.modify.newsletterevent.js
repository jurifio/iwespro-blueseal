window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newsletterevent-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita l'evento Campagna",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newsletterevent-edit', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una distinta per Cambiare la data"
        }).open();
        return false;
    }

    let newsletterEventId = selectedRows[0].id;
    let bsModal = new $.bsModal('Modifica', {
        body: '<p>Invia La Newsletter selezionata</p>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="eventName">Nome Evento</label>' +
        '<input autocomplete="on" type="text" id="eventName" ' +
        'class="form-control" name="eventName" value="' + selectedRows[0].eventName + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<div class=\"form-group form-group-default selectize-enabled\">' +
        '<label for=\"campaignId\">Seleziona la Campagna </label><select id=\"campaignId\" name=\"campaignId\" class=\"full-width selectpicker\" placeholder=\"Selezione la Campagna\"' +
        'data-init-plugin=\"selectize\"></select>' +
        ' </div>' +
        '</div>'
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'NewsletterCampaign'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#campaignId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            //labelField: 'name',
            labelField: ['name','dateCampaignStart','dateCampaignFinish'],
            searchField: ['name','dateCampaignStart','dateCampaignFinish'],
            options: res2,
            render: {
                option: function(item, escape) {

                    return '<div>'
                        + '<div>'
                        + '<strong>'
                        + 'Nome Campagna: '
                        + escape(item.name) + ''
                        + '</div>'
                        + '</strong>'
                        + '<strong>'
                        + 'Inizio Campagna:'
                        + '</strong>'
                        + escape(item.dateCampaignStart).substr(8,2)+'-'
                        + escape(item.dateCampaignStart).substr(5,2)+'-'
                        + escape(item.dateCampaignStart).substr(0,4)+' '
                        + escape(item.dateCampaignStart).substr(11,2)+':'
                        + escape(item.dateCampaignStart).substr(14,2)+':'
                        + escape(item.dateCampaignStart).substr(17,2)+' '
                        + '<strong>'
                        + 'Fine Campagna:'
                        + '</strong>'
                        + escape(item.dateCampaignFinish).substr(8,2)+'-'
                        + escape(item.dateCampaignFinish).substr(5,2)+'-'
                        + escape(item.dateCampaignFinish).substr(0,4)+' '
                        + escape(item.dateCampaignFinish).substr(11,2)+':'
                        + escape(item.dateCampaignFinish).substr(14,2)+':'
                        + escape(item.dateCampaignFinish).substr(17,2)+' '
                        + '</div>';
                },
                item: function(item, escape){
                    return '<div>'
                        + '<div>'
                        + '<strong>'
                        + 'Nome Campagna: '
                        + escape(item.name) + ''
                        + '</div>'
                        + '</strong>'
                        + '<strong>'
                        + 'Inizio Campagna:'
                        + '</strong>'
                        + escape(item.dateCampaignStart).substr(8,2)+'-'
                        + escape(item.dateCampaignStart).substr(5,2)+'-'
                        + escape(item.dateCampaignStart).substr(0,4)+' '
                        + escape(item.dateCampaignStart).substr(11,2)+':'
                        + escape(item.dateCampaignStart).substr(14,2)+':'
                        + escape(item.dateCampaignStart).substr(17,2)+' '

                        + '<strong>'
                        + 'Fine Campagna:'
                        + '</strong>'
                        + escape(item.dateCampaignFinish).substr(8,2)+'-'
                        + escape(item.dateCampaignFinish).substr(5,2)+'-'
                        + escape(item.dateCampaignFinish).substr(0,4)+' '
                        + escape(item.dateCampaignFinish).substr(11,2)+':'
                        + escape(item.dateCampaignFinish).substr(14,2)+':'
                        + escape(item.dateCampaignFinish).substr(17,2)+' '
                        + '</div>';
                }
            }
        });
    });



    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;
        let idCampaign = $('#campaignId').val();
        let name = $('#eventName').val();

        $.ajax({
            method: "put",
            url: "/blueseal/xhr/NewsletterEventManage",
            data: {
                id: id,
                campaignId: idCampaign,
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
