window.buttonSetup = {
    tag: "a",
    icon: "fa-bank",
    permission: "/admin/product/edit&&allShops",
    event: "bs-order-ModifyPayment",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Modifica Metodo di Pagamento",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-order-ModifyPayment', function () {
    var orderId = $.QueryString['order'];

    var modal = new $.bsModal('Modifica Ordine ', {
        body: '<label for="orderPaymentMethodId">Seleziona Il Nuovo Metodo di Pagamento</label><br />' +
            '<select id="orderPaymentMethodId" name="orderPaymentMethodId" class="full-width selectize"></select><br />'
    });

    let orderPaymentMethodId = $('select[name=\"orderPaymentMethodId\"]');


    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/ChangeOrderPaymentMethodAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            orderPaymentMethodId.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.name) + '</span> - ' +
                            '<span class="caption">' + escape(item.name) + '</span>' +
                            '</div>'
                    }
                }
            });
        });
    });


    modal.setOkEvent(function () {
       let  orderPaymentMethod = $('#orderPaymentMethodId').val();

        $.ajax({
            url: '/blueseal/xhr/ChangeOrderPaymentMethodAjaxController',
            method: 'POST',
            data: {
                orderId: orderId,
                orderPaymentMethod: orderPaymentMethod
            }
        }).done(function (res) {
            modal.writeBody(res);
        }).fail(function (res) {
            modal.writeBody(res);
        }).always(function (res) {
            modal.writeBody(res);
        });
    });

});