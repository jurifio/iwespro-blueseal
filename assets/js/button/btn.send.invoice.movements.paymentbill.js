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
                body: 'Scegli l\'opzione: <select id="invoiceOption">\n' +
                '<option value="invia">Invia fatture</option>\n' +
                '<option value="scarica">Scarica fatture</option>\n' +
                '</select>'
            }
        );

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            if($('#invoiceOption').val() === "invia") {
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
            } else if($('#invoiceOption').val() === "scarica"){
                $.ajax({
                    method: 'get',
                    url: '/blueseal/xhr/PaymentBillSendInvoiceMovements',
                    data: {
                        id: id
                    }
                }).done(function (res) {
                    let win = window.open("", "Title", "width=1920,height=1080");
                    win.document.write(res);
                    win.document.close();
                    win.onload = function() { // wait until all resources loaded
                        win.focus(); // necessary for IE >= 10
                        win.print();  // change window to mywindow
                        win.close();// change window to mywindow
                    };
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
            }
        });


    } else {
        new Alert({
            type: "warning",
            message: "Seleziona una sola riga"
        }).open();
        return false;
    }
    });