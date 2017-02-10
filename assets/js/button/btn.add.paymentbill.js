window.buttonSetup = {
    tag: "a",
    icon: "fa-file-o fa-plus",
    permission: "/admin/order/list&&allShops",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi una nuova distinta",
    placement: "bottom",
    event: "btn.add.paymentbill"
};

$(document).on('btn.add.paymentbill', function (a, b, c) {
    "use strict";

    let today = new Date().toISOString().slice(0, 10);
    let modal = new $.bsModal('Aggiungi una nuova distinta', {
            body: '<div class="col-xs-12>">' +
            '<label for="paymentDate">Data di Pagamento</label>' +
            '<input autocomplete="off" type="date" id="paymentDate" ' +
            'class="form-control" name="paymentDate" value="' + today + '">' +
            '</div>'
        }
    );

    modal.setOkEvent(function () {
        modal.setOkEvent(function () {
            modal.hide();
            $('.table').DataTable().ajax.reload(null, false);
        });
        let date = $('#paymentDate').val();
        modal.showLoader();
        $.ajax({
            method: "post",
            url: "/blueseal/xhr/PaymentBillManage",
            data: {
                paymentDate: date
            },
            dataType: "json"
        }).done(function (res) {
            modal.writeBody('Creata distinta numero: '+res.id);
        });
    });
});