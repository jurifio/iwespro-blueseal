window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-editorialplan-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita il Piano Editoriale",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-editorialplan-edit', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un Piano Editoriale per Cambiare la data"
        }).open();
        return false;
    }

    let editorialPlanId = selectedRows[0].id;
    let bsModal = new $.bsModal('Invio', {
        body: '<p>Modifica il Piano Editoriale Selezionato</p>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="editorialPlanSocialName">Nome Media</label>' +
        '<input autocomplete="on" type="text" id="editorialPlanSocialName" ' +
        'class="form-control" name="editorialPlanSocialName" value="' + selectedRows[0].name + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="editorialPlanSocialIcon">icona </label>' +
        '<input autocomplete="on" type="text" id="editorialPlanSocialIcon" ' +
        'class="form-control" name="editorialPlanSocialName" value="' + selectedRows[0].iconSocial + '">' +
        '</div>' +
        '</div>'

    });



    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;
        let name = $('#editorialPlanSocialName').val();
        let iconSocial = $('#editorialPlanSocialIcon').val();

        $.ajax({
            method: "put",
            url: "/blueseal/xhr/EditorialPlanSocialManage",
            data: {
                id: id,
                name: name,
                iconSocial:iconSocial,
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
