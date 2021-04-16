window.buttonSetup = {
    tag: "a",
    icon: "fa-eraser",
    permission: "/admin/product/edit",
    event: "bs-product-look.delete",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cancella Un Look",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-product-look.delete', function () {
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


    let bsModal = new $.bsModal('Cancella un Look fra Prodotti', {
        body: `<p>Attenzione tutte le relazioni  tra I Prodotti  per creare il look saranno cancellate</p>
<p>Sei Sicuro di Cancellare il look?</p>
          `
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {



        const data = {
            id:id,
        };
        $.ajax({
            method: 'delete',
            url: '/blueseal/xhr/ProductLookAjaxController',
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