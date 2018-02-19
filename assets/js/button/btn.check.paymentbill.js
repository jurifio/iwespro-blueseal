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

        let text = '';
            $.ajax({
                method: 'get',
                url: '/blueseal/xhr/PaymentBillCheck',
                data: {}
            }).done(function (res) {
                let ris = JSON.parse(res);

                ris[0].forEach(function(element) {
                    text += '<input type="checkbox" id="check" data-element="' + element[0] + '" data-sum="' + element[1] + '">' + 'id: ' + element[0] + '<br />'
                });



                bsModal.writeBody(text);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {

                    let checked = [];
                    let i = 0;

                    $('#check:checked').each(function () {
                       checked[i] = [$(this).data('element'), $(this).data('sum')];
                       i++;
                    });

                    $.ajax({
                        method: 'put',
                        url: '/blueseal/xhr/PaymentBillCheck',
                        data: {
                            checked: checked
                        }
                    }).done(function (res) {
                        bsModal.writeBody(res);
                    }).fail(function (res) {
                        bsModal.writeBody('Errore grave');

                    bsModal.hide();
                    window.location.reload();
                });
                bsModal.showOkBtn();
            });
});