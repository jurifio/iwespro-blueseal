window.buttonSetup = {
    tag:"a",
    icon:"fa-list",
    permission:"/admin/product/edit&&allShops",
    event:"bs-product-storehouse-quantity",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Visualizza Quantita per negozio",
    placement:"bottom"
};

$(document).on('bs-product-storehouse-quantity', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }
    var product = selectedRows[0].DT_RowId;
    var shopId = selectedRows[0].DT_RowId;


    let bsModal = new $.bsModal('Disponibilita per negozio', {
        body: 'Disponibilità'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data = {
            product: product,
        };
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/ProductStorehouseQuantityListAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();

                bsModal.hide();
            });
            bsModal.showOkBtn();
        });
    });
})
;