window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs-lists-add-editorialplandetail",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Edita il Piano Editoriale",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-lists-add-editorialplandetail', function (e, element, button) {

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
        body: '<p>Aggiungi un Dettaglio</p>'
    });
    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop'
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


        $.ajax({
            method: "get",
            url: "/blueseal/xhr/EditorialPlanDetailListAjaxController",
            data: {
                editorialPlanId: id
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
