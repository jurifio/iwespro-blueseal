window.buttonSetup = {
    tag: "a",
    icon: "fa-eur",
    permission: "/admin/product/delete&&allShops",
    event: "bs-paymentActiveBill-pay",
    class: "btn btn-default",
    rel: "tooltip",
    title: "paga La Distinta",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-paymentActiveBill-pay', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una distinta per Registrare il pagamento"
        }).open();
        return false;
    }

    let selectedRow = dataTable.row('.selected').data();
    let paymentBillId = selectedRow.DT_RowId;

    let modal = new $.bsModal('Paga la Distinta e tutte le sue relative Scadenze', {});
    modal.showLoader();

    Pace.ignore(function () {
        $.ajax({
            method: "get",
            url: "/blueseal/xhr/BillRegistryActivePaymentBillPayManage",
            data: {
                paymentBillId: paymentBillId
            },
            dataType: "json"
        }).done(function (paymentBill) {
            let now;
            if(paymentBill.paymentDate){
                now = paymentBill.paymentDate.substr(0,10);
            } else {
                now = new Date().toISOString().slice(0, 10);
            }
            var day = paymentBill.paymentDate.substr(8,2);
            var month = paymentBill.paymentDate.substr(5,2);
            var year = paymentBill.paymentDate.substr(0,4);
            var today = (year)+ "-" + (month) + "-" + (day) ;
            modal.writeBody('<div class="row">' +
                '<div class="col-xs-6>">' +
                '<label for="paymentDate">Data di Pagamento</label>' +
                '<input autocomplete="off" type="date" id="paymentDate" ' +
                'class="form-control" name="paymentDate" value="' + today + '">' +
                '</div>' +
                '</div>'+
                '<div class="col-xs-6>">' +
                '<label for="amountPayment">Importo Pagato</label>' +
                '<input autocomplete="off" type="text" id="amountPayment" ' +
                'class="form-control" name="amountPayment" value="' + paymentBill.amount + '">' +
                '</div>' +
                '</div>'
            );

            modal.setOkEvent(function () {
                paymentBill.paymentDate = $('#paymentDate').val();
                paymentBill.amount = $('#amountPayment').val();
                modal.showLoader();
                modal.setOkEvent(function () {
                    modal.hide();
                    $('.table').DataTable().ajax.reload(null, false);
                });
                Pace.ignore(function () {
                    $.ajax({
                        method: "put",
                        url: "/blueseal/xhr/BillRegistryActivePaymentBillPayManage",
                        data: {
                            paymentBill: paymentBill

                        },
                    }).done(function (res2) {
                        modal.writeBody('Dati Correttamente Aggiornati');
                    }).fail(function (res) {
                        modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
                        console.error(res);
                    });
                });
            });
        }).fail(function () {
            modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
        });


    });
});
