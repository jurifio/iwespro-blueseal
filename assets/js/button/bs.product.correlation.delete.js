window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/product/edit",
    event: "bs-product-correlation.delete",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella Una Correlazione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-correlation.delete', function () {
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }
    var id =selectedRows[0].DT_RowId;
    var remoteId=selectedRows[0].remoteId;
    var remoteShopId=selectedRows[0].remoteShopId;


    let bsModal = new $.bsModal('Cancella un Tema di Correlazione fra Prodotti', {
        body: `<p>Attenzione tutte le correlazioni tra I Prodotti saranno cancellate</p>
<p>Sei Sicuro di Cancellare questa Correlazione?</p>
          `
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
            id:id,
            shopId:remoteShopId,
            remoteId:remoteId
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/ProductCorrelationAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function () {
            bsModal.writeBody('Si è verificato un errore')
        }).always(function () {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $('.table').DataTable().ajax.reload();
            });
        });
    });
});