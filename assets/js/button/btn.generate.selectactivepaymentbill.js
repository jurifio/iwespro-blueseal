window.buttonSetup = {
    tag: "a",
    icon: "fa-cogs",
    permission: "/admin/order/list&&allShops",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Genera distinte",
    placement: "bottom",
    event: "btn-generate-selectactivepaymentbill"
};

$(document).on('btn-generate-selectactivepaymentbill', function (a, b, c) {
    "use strict";

    let selectedInvoice = [];
    let selectedRows = $('.table').DataTable().rows('.selected').data();

    //id-variantId in array
    $.each(selectedRows, function (k, v) {
        selectedInvoice.push(v.DT_RowId);
    });

    let numberOfInvoice = selectedInvoice.length;

    if(numberOfInvoice == 0){
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno una fattura"
        }).open();
        return false;
    }

var isProgrammable='0';


    let today = new Date().toISOString().slice(0, 10);
    let modal = new $.bsModal('Crea clientSlip | Distinta Attiva per le Fatture Selezionate', {
            body: 'Confermi l\'operazione'


        }
    );




    modal.setOkEvent(function () {
        modal.showCancelBtn();
            $('.table').DataTable().ajax.reload(null, false);




        $.ajax({
            method: "post",
            url: "/blueseal/xhr/BillRegistryGenerateSelectActivePaymentManageAjaxController",
            data: {
                selectedInvoice:selectedInvoice

            }
        }).done(function (res) {
            modal.writeBody(res);
        }).fail(function (res) {
            modal.writeBody(res);
        }).always(function (res) {
            modal.setOkEvent(function () {
                window.location.reload();
                modal.hide();
                // window.location.reload();
            });
            modal.showOkBtn();
        });
    });
});

$(document).ready(function() {




});

