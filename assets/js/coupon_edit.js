$(document).on('bs.coupon.edit', function() {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Modifica Coupon');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    $.ajax({
        type: "PUT",
        url: "#",
        data: $('form').serialize()
    }).done(function (content){
        body.html("Salvataggio riuscito");
        bsModal.modal();
    }).fail(function (){
        body.html("Errore grave");
        bsModal.modal();
    });
});
$(document).ready(function () {

    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop',
            condition :{hasEcommerce:1}
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#remoteShopId');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            options: res2,
            onInitialize: function () {
                var selectize = this;
                selectize.setValue($('#shopSelected').val());
            }
        });
    });

    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'CouponType'
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#couponTypeId');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            options: res
        });
    });


});