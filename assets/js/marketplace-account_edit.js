$(document).on('bs.marketplace-account.save', function () {
    let method;
    let data = {};

    let inputs = $('#config-list input');
    for(let k in inputs){
        "use strict";
        if(!inputs.hasOwnProperty(k)) continue;
        let v = $(inputs[k]);
        data = readFullInput(v.attr('name'),v.val(),data);
    }

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
                '<div class="row">' +
                    '<div class="col-md-offset-{{offset}} col-md-{{colLength}}">' +
                        '<div class="form-group form-group-default required">' +
                            '<label for="{{field}}">{{label}}</label>' +
                            '<input id="{{field}}" autocomplete="off" type="text" class="form-control" ' +
                                    'name="{{field}}" value="" required="required"/>' +
                        '</div>' +
                    '</div>' +
                '</div>';

            $('#marketplace_account_marketplace_id').val(res.id);
            $('#marketplace_account_id').val(res.title);
            $('#marketplace_account_name').val(res.name);
            let box = $('#config-list');
            drawObject("config",res.config,inputMock,box,0);
        });
    }
})(jQuery);

function drawObject(prefix, object, inputMock, box,offset) {
    "use strict";
    if(prefix != '') box.append($('<p>'+prefix+'</p>'));
    for (let prop in object) {
        if (object.hasOwnProperty(prop) && typeof object[prop] != 'function' ) {
            if(typeof object[prop] == 'object' && prefix == '') drawObject(prop,object[prop],inputMock,box,offset);
            else if(typeof object[prop] == 'object') drawObject(prefix+'_'+prop,object[prop],inputMock,box,offset+1);
            else drawInput(prefix,prop,object[prop],inputMock,box,offset+1);
        }
    }
    if(prefix != '') box.append($('<p>/'+prefix+'</p>'));
}

function drawInput(prefix,key,val,inputMock,box,offset) {
    let newInput = $(inputMock.monkeyReplaceAll('{{field}}',prefix+'_'+key).
                                replaceAll('{{label}}',key).
                                replaceAll('{{offset}}',offset).
                                replaceAll('{{colLength}}',12-offset));
    newInput.find('input').val(val);
    box.append(newInput);
}

function readFullInput(name,value,object) {
    "use strict";
    let pieces = name.split('_');
    if(pieces.length == 1) object[name] = value;
    else {
        let firstPiece = pieces[0];
        pieces.splice(0,1);
        let newObject = {};
        if(typeof object[firstPiece] != 'undefined') newObject = object[firstPiece];
        object[firstPiece] = readFullInput(pieces.join('_'),value,newObject);
    }
    return object;
}