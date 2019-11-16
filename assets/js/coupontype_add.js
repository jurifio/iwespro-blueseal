$(document).on('bs.coupontype.add', function() {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Aggiungi un tipo coupon');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    $.ajax({
        type: "POST",
        url: "#",
        data: $('form').serialize()
    }).done(function (content){
        body.html("Salvataggio riuscito");
        bsModal.modal();
        okButton.off().on('click', function() {
            bsModal.modal('hide');
            okButton.off();
            window.location.href = '/blueseal/tipocoupon/modifica/'+content;
        });
    }).fail(function(){
        body.html('Errore grave');
        bsModal.modal();
    });
});

$(document).ready(function () {
    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Tag'
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#tags');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'slug',
            searchField: ['slug'],
            options: res,
            maxItems: 50
        });
    });

    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Campaign'
        },
        dataType: 'json'
    }).done(function (res) {
        var select = $('#campaignId');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            options: res
        });
    });

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
        });
    });
});