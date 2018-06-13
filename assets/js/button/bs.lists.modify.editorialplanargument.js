window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-editorialplanargument-edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita l\'Argomento",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-editorialplanargument-edit', function (e, element, button) {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un Argomento per Modificarlo"
        }).open();
        return false;
    }

    let editorialPlanId = selectedRows[0].id;
    let bsModal = new $.bsModal('Invio', {
        body: '<p>Modifica l\'Argomento Selezionato</p>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="editorialPlanArgumentTitleArgument">Nome Argomento</label>' +
        '<input autocomplete="on" type="text" id="editorialPlanArgumentTitleArgument" ' +
        'class="form-control" name="editorialPlanArgumentTitleArgument" value="' + selectedRows[0].titleArgument + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="editorialPlanArgumentType">Tipo Argomento </label>' +
        '<input autocomplete="on" type="text" id="editorialPlanArgumentType" ' +
        'class="form-control" name="editorialPlanArgumentType" value="' + selectedRows[0].type + '">' +
        '</div>'+
        '</div>'+
        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="editorialPlanArgumentDescription">Descrizione Argomento </label> ' +
        '<textarea id=\"editorialPlanArgumentDescription\" class=\"form-control\" ' +
        'name="editorialPlanArgumentDescription">' + selectedRows[0].descriptionArgument + '</textarea>' +
        '</div>' +
        '</div>'

    });



    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;
        let titleArgument = $('#editorialPlanArgumentTitleArgument').val();
        let type = $('#editorialPlanArgumentType').val();
        let descriptionArgument =$('#editorialPlanArgumentDescription').val();

        $.ajax({
            method: "put",
            url: "/blueseal/xhr/EditorialPlanArgumentManage",
            data: {
                id: id,
                titleArgument: titleArgument,
                type:type,
                descriptionArgument:descriptionArgument,
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
