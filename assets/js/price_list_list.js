$(document).on('bs.pricelist.delete', function() {

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare un listino alla volta"
        }).open();
        return false;
    }
    var id =selectedRows[0].DT_RowId;
    var shopId=selectedRows[0].shopId;
    var remoteShopId=selectedRows[0].remoteShopId;


    let bsModal = new $.bsModal('Cancella Il listino selezinoato', {
        body: `<p>Sei Sicuro di Cancellare questo Lisitno?</p>
          `
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
            id:id,
            shopId:shopId
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/ProductListListAjaxController',
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