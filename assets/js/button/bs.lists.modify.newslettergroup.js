window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newsletterevent-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita il Gruppo Newsletter",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newslettergroup-edit', function (e, element, button) {

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
        '<label for="eventName">Nome Gruppo</label>' +
        '<input autocomplete="on" type="text" id="name" ' +
        'class="form-control" name="name" value="' + selectedRows[0].name + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<div class=\"form-group form-group-default selectize-enabled\">' +
        '<label for=\"sql\">Modifica sql </label><textarea id=\"sql\" name=\"campaignId\" class=\"full-width selectpicker\" placeholder=\"Modifica l\'istruzione sql\"' +
        'value=\"' + selectedRows[0].sql + '\"' +
        '</textarea>' +
        ' </div>' +
        '</div>'
    });




    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;
        let sql = $('#sql').val();
        let name = $('#name').val();

        $.ajax({
            method: "put",
            url: "/blueseal/xhr/NewsletterEventManage",
            data: {
                id: id,
                sql: sql,
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
