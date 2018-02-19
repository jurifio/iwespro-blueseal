window.buttonSetup = {
    tag: "a",
    icon: "fa fa-check-square",
    permission: "/admin/order/list&&allShops",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Controlla distinte incongruenti",
    placement: "bottom",
    event: "btn-check-paymentbill"
};

$(document).on('btn-check-paymentbill', function () {
    "use strict";


        let bsModal = new $.bsModal('Controlla congruenza distinte', {
                body: 'Controlla se la sommma delle fatture corrisponde alla somma della distinta di pagamento'
            }
        );

        bsModal.setOkEvent(function () {
            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/PaymentBillCheck',
                data: {}
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
});