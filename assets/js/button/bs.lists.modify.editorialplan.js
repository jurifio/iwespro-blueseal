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
        '<label for="editorialPlanName">Nome Campagna</label>' +
        '<input autocomplete="on" type="text" id="editorialPlanName" ' +
        'class="form-control" name="editorialPlanName" value="' + selectedRows[0].name + '">' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-12">' +

        '<label for="shopId">Seleziona lo Shop </label>'+
        '<select id="shopId" name="shopId"'+
        'class="full-width selectpicker"'+
        'placeholder="Seleziona la Lista"'+
        'data-init-plugin="selectize" value="' +selectedRows[0].shopId+'">'+
        '</select>'+
        '</div>'+
        '</div>'+

        '<div class="row">' +
        '<div class="col-xs-6>">' +
        '<label for="startDate">Data di Inizio Piano Editoriale</label>' +
        '<input autocomplete="off" type="datetime-local" id="startDate"' +
        'class="form-control" name="startDate" value=\"' + selectedRows[0].startDate + '">' +
        '</div>' +
        '</div>' +
        '<div class=\"row\">' +
        '<div class=\"col-xs-6>\">' +
        '<label for="endDate">Data di Fine Piano Editoriale</label>' +
        '<input autocomplete="off" type="datetime-local" id="endDate"' +
        'class="form-control" name="endDate" value="' + selectedRows[0].endDate + '">' +
        '</div>' +
        '</div>'
    });
    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop',
            selection: {id: selectedRows[0].shopId}
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#shopId');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });


    bsModal.setOkEvent(function () {

        let id = selectedRows[0].id;
        let dateStartDate = $('#startDate').val();
        let dateEndDate = $('#endDate').val();
        let name = $('#editorialPlanName').val();
        let shopId = $('#shopId').val();

        $.ajax({
            method: "put",
            url: "/blueseal/xhr/EditorialPlanManage",
            data: {
                id: id,
                dateStartDate: dateStartDate,
                dateEndDate: dateEndDate,
                name: name,
                shopId:shopId,
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
