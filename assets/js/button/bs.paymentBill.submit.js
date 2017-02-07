window.buttonSetup = {
    tag: "a",
    icon: "fa-check-square-o",
    permission: "/admin/product/delete&&allShops",
    event: "bs.paymentBill.submit",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Segna distinta come Sottomessa",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.paymentBill.submit', function (e, element, button) {

    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();

    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una o distinta per Sottometterla"
        }).open();
        return false;
    }

    let selectedRow = dataTable.row('.selected').data();
    let paymentBill = selectedRow.DT_RowId;

    let modal = new $.bsModal(button.getTitle(), {
        body: '<span>Sei sicuro di voler sottomettere la distinta?</span>'
    });
    modal.setOkEvent(function () {
        modal.setOkEvent(function () {
            modal.hide();
            $('.table').DataTable().ajax.reload(null, false);
        });
        modal.showLoader();

        Pace.ignore(function () {
            $.ajax({
                url: '/blueseal/xhr/PaymentBillSubmit',
                data: {
                    paymentBillId: paymentBill
                },
                dataType: 'json',
                method: 'put'
            }).done(function (paymentBill) {
                modal.writeBody('aggiornato')

            });

        });
    });

});
