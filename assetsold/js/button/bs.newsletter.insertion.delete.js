window.buttonSetup = {
    tag: "a",
    icon: "fa-close",
    permission: "/admin/product/delete&&allShops",
    event: "bs-newsletter-insertion-delete",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Elimina un'inserzione",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-newsletter-insertion-delete', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let insertionId = selectedRows[0].row_id;

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Puoi eliminare un'inserzione alla volta"
        }).open();
        return false;
    }

    let bsModal = new $.bsModal('Modifica Inserzione', {
        body: `<p>Sicuro di voler eliminare l'inserzione?</p>`
    });

    bsModal.setOkEvent(function () {

        $.ajax({
            method: "delete",
            url: "/blueseal/xhr/NewsletterInsertionManage",
            data: {
                insertionId: insertionId
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable()
            });
            bsModal.showOkBtn();
        });
    });
});
