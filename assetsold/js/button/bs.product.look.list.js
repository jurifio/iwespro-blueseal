window.buttonSetup = {
    tag: "a",
    icon: "fa-list",
    permission: "/admin/product/edit",
    event: "bs-product-look.list",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Visualizza prodotti associati al Look",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-look.list', function () {
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }
    var id = selectedRows[0].DT_RowId;


    let bsModal = new $.bsModal('Modifica un Look fra Prodotti', {
        body: `Lista Prodotti associati al look`
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {


        const data = {
            code: id,


        };
        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/ProductHasProductLookListManageAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Si è verificato un errore')
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
            });
        });
    });
});