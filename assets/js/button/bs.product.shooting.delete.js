window.buttonSetup = {
    tag: "a",
    icon: "fa-times",
    permission: "/admin/product/edit&&allShops",
    event: "bs-product-shooting-delete",
    class: "btn btn-default",
    rel: "tooltip",
    title: " Cancella shooting del Prodotto",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-shooting-delete', function (e, element, button) {
    var dataTable = $('.table').DataTable();
    let products = [];
    let shop = [];
    let getVarsArray = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    let selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno o più Prodotti per poterli taggare"
        }).open();
        return false;
    }

    let i = 0;
    let pieces = 0;
    $.each(selectedRows, function (k, v) {
        if (v.shooting != 'no') {
            products.push(v.DT_RowId);
            i++;
        }

    });

    let bsModal = new $.bsModal('Cancella prodotti in shooting', {
        body: 'Vuoi Canellare i Prodotti selezionati dallo Shooting'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            products: products,
        };
        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/ProductShootingDeleteAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();

                bsModal.hide();
                dataTable.ajax.reload();
            });
            bsModal.showOkBtn();
        });
    });
})
;

