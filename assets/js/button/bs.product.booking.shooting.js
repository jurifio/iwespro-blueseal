window.buttonSetup = {
    tag:"a",
    icon:"fa-address-book",
    permission:"shooting",
    event:"bs-product-booking-shooting",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Prenota shooting",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-booking-shooting', function (e, element, button) {

    let bsModal = new $.bsModal('Prenota uno shooting', {
        body: '<p>Prenota un nuovo shooting</p>' +
        '<div class="form-group form-group-default required">' +
        '<label for="selectFriend">Seleziona una data d\'invio</label>' +
        '<input type="date" id="bookingDate">' +
        '</div>' +
        '<div class="form-group form-group-default required">' +
        '<label for="selectShop">Seleziona lo shop</label>' +
        '<select id="selectShop" name="selectShop"></select>' +
        '</div>' +
        '<div class="form-group form-group-default required" id="typeCheck">' +
        '</div>'
    });


        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/ShootingBookingAjaxController'
        }).done(function (res) {

            let type = JSON.parse(res);

            $.each(type["tp"], function(k, v) {
                $('<input />', { type: 'number', id: v.id, class: 'toGet' }).appendTo($("#typeCheck"));
                $('<label />', { 'for': v.id, text: v.name }).appendTo($("#typeCheck"));
            });

            $.each(type["sh"], function(k, v) {
                $('#selectShop') .append($("<option/>") .val(v.id) .text(v.name))
            });
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let cat = [];
            $.each($(".toGet"), function(){

                if ($(this).val() !== ""){
                    cat.push({
                        key:   $(this).attr('id'),
                        value: $(this).val()
                    });
                }

                //cat.push($(this).val());
            });

            const data = {
                date: $('#bookingDate').val(),
                cat: cat,
                shop: $('#selectShop').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ShootingBookingAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });

});