window.buttonSetup = {
    tag:"a",
    icon:"fa-money",
    permission:"/admin/product/edit&&allShops",
    event:"bs-order-pay",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Modifica lo stato di pagamento",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-order-pay', function () {
    var orderId = $.QueryString['order'];

    modal = new $.bsModal(
        'Imposta Stato Pagamento',
        {
            body: '',
            isCancelButton: true,
        }
    );

    $.ajax({
        url: '/blueseal/xhr/getTableContent',
        method: 'GET',
        data: {
            table: 'Order',
            fields: ['paidAmount'],
            condition: {id: orderId}
        }
    }).done(function(res){
        res = JSON.parse(res);

        var paid = res[0].paidAmount;
        var body = '';
        var toPay = null;
        if (paid === null) {
            body = "vuoi segnare questo ordine come pagato?";
            toPay = 1;
        } else {
            body = "vuoi togliere lo stato di pagato da questo ordine?";
            toPay = 0;
        }

        modal.writeBody(body);
        if (null !== toPay) {
            modal.setOkEvent(function () {
                $.ajax({
                    url: '/blueseal/xhr/OrderPay',
                    method: 'POST',
                    data: {orderId: orderId, toPay: toPay}
                }).done(function (res) {
                    modal.writeBody(res);
                }).fail(function (res) {
                    modal.writeBody('OOPS! ' + res.responseText);
                    console.error(res);
                }).always(function () {
                    modal.hideCancelBtn();
                    modal.setOkEvent(function () {
                        modal.hide();
                    });
                });
            });
        }
    }).fail(function(res){
        modal = $.bsModal(
            'Imposta Stato Pagamento',
            {
                body: '',
                isCancelButton: true,
            }
        );

        modal.writeBody("OOPS! C'è stato un problema nel recupero delle informazioni");
        console.error(res);
        modal.hideCancelBtn();
        modal.setOkEvent(function(){
            modal.hide();
        });
    });
});