window.buttonSetup = {
    tag: "a",
    icon: "fa-pencil-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs.paymentBill.edit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Cambia Data",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.paymentBill.edit', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una distinta per Cambiare la data"
        }).open();
        return false;
    }

    let selectedRow = dataTable.row('.selected').data();
    let paymentBillId = selectedRow.DT_RowId;

    let modal = new $.bsModal('Cambia Data Pagamento', {});
    modal.showLoader();

    Pace.ignore(function () {
        $.ajax({
            method: "get",
            url: "/blueseal/xhr/PaymentBillManage",
            data: {
                paymentBillId: paymentBillId
            },
            dataType: "json"
        }).done(function (paymentBill) {
            let today;
            if(paymentBill.paymentDate) today = paymentBill.paymentDate;
            else today = new Date().toISOString().slice(0, 10);
            modal.writeBody('<div class="row">' +
                '<div class="col-xs-6>">' +
                '<label for="paymentDate">Data di Pagamento</label>' +
                '<input autocomplete="off" type="date" id="paymentDate" ' +
                'class="form-control" name="paymentDate" value="' + today + '">' +
                '</div>' +
                '</div>'
            );

            modal.setOkEvent(function () {
                paymentBill.paymentDate = $('#paymentDate').val();
                modal.showLoader();
                modal.setOkEvent(function () {
                    modal.hide();
                    $('.table').DataTable().ajax.reload(null, false);
                });
                Pace.ignore(function () {
                    $.ajax({
                        method: "put",
                        url: "/blueseal/xhr/PaymentBillManage",
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
