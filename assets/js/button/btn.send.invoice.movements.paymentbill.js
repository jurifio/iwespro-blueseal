window.buttonSetup = {
    tag: "a",
    icon: "fa fa-exchange",
    permission: "/admin/order/list&&allShops",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Manda la lista delle fatture",
    placement: "bottom",
    event: "btn-send-invoice-movements-paymentbill"
};

$(document).on('btn-send-invoice-movements-paymentbill', function () {
    "use strict";

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();
    let id = selectedRows[0].id;

    if (selectedRows.length === 1) {

        let bsModal = new $.bsModal('Controlla congruenza distinte', {
                body: 'Sei sicuro di voler inviare la mail al friend?'
            }
        );

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/PaymentBillSendInvoiceMovements',
                data: {
                    id: id
                }
            }).done(function (res) {
                bsModal.writeBody('Mail inviata con successo');
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });
    } else {
        new Alert({
            type: "warning",
            message: "Seleziona una sola riga"
        }).open();
        return false;
    }

});