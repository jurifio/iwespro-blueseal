window.buttonSetup = {
    tag: "a",
    icon: "fa-print",
    permission: "/admin/order/list&&allShops",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Stampal la lista delle fatture",
    placement: "bottom",
    event: "btn-print-invoice-movements-activepaymentbill"
};

$(document).on('btn-print-invoice-movements-activepaymentbill', function () {
    "use strict";

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();
    let id = selectedRows[0].id;

    if (selectedRows.length === 1) {

        let bsModal = new $.bsModal('Stampa La Distinta', {
                body: 'Stampa'
            }
        );

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            window.open('/blueseal/xhr/BillRegistryActivePaymentSlipPrintAjaxController?id='+id,'_blank');
        });


    } else {
        new Alert({
            type: "warning",
            message: "Seleziona una sola riga"
        }).open();
        return false;
    }
    });