$(document).on('bs.marketplace-account.save', function () {
    let method;
    let data = {};



    data.billingAddressBook = readShipment('#billingAddress');
    data.shippingAddresses = [];
    $.each($('#shippingAddresses .shippingAddress'), function (k, v) {
        data.shippingAddresses.push(readShipment(v));
    });

    if (data.id.length) {
        method = "PUT";
    } else {
        method = "POST";
    }

    $.ajax({
        method: method,
        url: "/blueseal/xhr/ShopManage",
        data: {
            shop: data
        }
    }).done(function () {
        new Alert({
            type: "success",
            message: "Modifiche Salvate"
        }).open();
    }).fail(function (e) {
        console.log(e);
        new Alert({
            type: "danger",
            message: "Impossibile Salvare"
        }).open();
    });
});

(function ($) {
    let params = $.decodeGetStringFromUrl(window.location.href);
    if (typeof params.id != 'undefined') {
        $.ajax({
            url: "/blueseal/xhr/MarketplaceAccountManage",
            data: {
                id: params.id
            },
            dataType: "json"
        }).done(function (res) {
            let inputMock =
                '<div class="col-md-12">' +
                    '<div class="form-group form-group-default required">' +
                        '<label for="{{field}}">{{label}}</label>' +
                        '<input id="{{field}}" autocomplete="off" type="text" class="form-control" ' +
                                'name="{{field}}" value="" required="required"/>' +
                    '</div>' +
                '</div>';

            $('#marketplace_account_marketplace_id').val(res.id);
            $('#marketplace_account_id').val(res.title);
            $('#marketplace_account_name').val(res.name);
            let box = $('#config-list');
            drawObject("config",res.config,inputMock,box);
        });
    }
})(jQuery);

function drawObject(prefix, object,inputMock,box) {
    "use strict";
    if(prefix != '') box.append($('<p>'+prefix+'</p>'));
    for (let prop in object) {
        if (object.hasOwnProperty(prop) && typeof object[prop] != 'function' ) {
            if(typeof object[prop] == 'object' && prefix == '') drawObject(prop,object[prop],inputMock,box);
            else if(typeof object[prop] == 'object') drawObject(prefix+'_'+prop,object[prop],inputMock,box);
            else drawInput(prefix,prop,object[prop],inputMock,box);
        }
    }
    if(prefix != '') box.append($('<p>/'+prefix+'</p>'));
}

function drawInput(prefix,key,val,inputMock,box) {
    let newInput = $(inputMock.replaceAll('{{field}}',prefix+'_'+key).replaceAll('{{label}}',key));
    newInput.find('input').val(val);
    box.append(newInput);
}