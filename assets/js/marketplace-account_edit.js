$(document).on('bs.marketplace-account.save', function () {
    let collectData = {};
    var config = '{';
    let qualifierTypeText = '';
    let key = '';
    let cicleMax=$("#countField").val()+2;
    $('input, select').each(
        function (index) {
            qualifierTypeText = '';
            var input = $(this);
            if ($.isNumeric($(input).val())) {
                qualifierTypeText = '';
            } else {
                qualifierTypeText = '"';
            }
            key = input.attr('name');
           if(cicleMax>=index){

                        config = config + '"' + key + '":' + qualifierTypeText + input.val() + qualifierTypeText + ',';

                }
        });
    config = config.replace(/config_/g, '');
    let n = config.indexOf("productSizeGroupId1");
    config= config.substring(0, n - 2);
    config=config+'}';
    /*let inputs = $('#config-list input');
    for (let k in inputs) {
        "use strict";
        if (!inputs.hasOwnProperty(k)) continue;
        let v = $(inputs[k]);
        if (k < $("#countField").val()) {
            collectData = readFullInput(v.attr('name'), v.val(), collectData);
        }
    }*/



    $.ajax({
        method: "POST",
        url: "/blueseal/xhr/MarketplaceAccountManage",
        data: {
            collect: config,
            marketplaceAccountId: $('#marketplaceAccountId').val(),
            marketplaceId: $('#marketplaceId').val(),
            marketplace_account_name: $('#marketplace_account_name').val(),

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
                '<div class="col-md-12">' +
                //'<div class="col-md-offset-{{offset}} col-md-{{colLength}}">' +
                '<div class="form-group form-group-default required">' +
                '<label for="{{field}}" id="label{{field}}">{{label}}</label>' +
                '<input id="{{field}}" autocomplete="off" type="text" class="form-control" ' +
                'name="{{field}}" value="" required="required"/>' +
                ' <span class="bs red corner label"><i\n' +
                'class="fa fa-asterisk"></i></span>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('#marketplace_account_marketplace_id').val(res.id);
            $('#marketplace_account_id').val(res.title);
            $('#marketplace_account_name').val(res.name);
            let box = $('#config-list');
            drawObject("config", res.config, inputMock, box, 0);
        });
    }
})(jQuery);

function drawObject(prefix, object, inputMock, box, offset) {
    "use strict";
    if (prefix != '') box.append($('<p>' + prefix + '</p>'));
    for (let prop in object) {
        if (object.hasOwnProperty(prop) && typeof object[prop] != 'function') {
            if (typeof object[prop] == 'object' && prefix == '') drawObject(prop, object[prop], inputMock, box, offset);
            else if (typeof object[prop] == 'object') drawObject(prefix + '_' + prop, object[prop], inputMock, box, offset + 1);
            else drawInput(prefix, prop, object[prop], inputMock, box, offset + 1);
        }
    }
    if (prefix != '') box.append($('<p>/' + prefix + '</p>'));
}

function drawInput(prefix, key, val, inputMock, box, offset) {
    let newInput = $(inputMock.monkeyReplaceAll('{{field}}', prefix + '_' + key).monkeyReplaceAll('{{label}}', key).monkeyReplaceAll('{{offset}}', offset).monkeyReplaceAll('{{colLength}}', 12 - offset));
    newInput.find('input').val(val);
    box.append(newInput);
}


function readFullInput(name, value, object) {
    "use strict";
    let pieces = name.split('_');
    if (pieces.length == 1) object[name] = value;
    else {
        let firstPiece = pieces[0];
        pieces.splice(0, 1);
        let newObject = {};
        if (typeof object[firstPiece] != 'undefined') {
            newObject = object[firstPiece];
        }
        object[firstPiece] = readFullInput(pieces.join('_'), value, newObject);

    }
    return object;
}

$(document).ready(function () {
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId1');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId2');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId3');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId4');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupId5');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $('#productSizeGroupId1').change(function () {
        let productSizeGroupId1 = $('#productSizeGroupId1').val();
        $('#config_productSizeGroup1').val(productSizeGroupId1);


    });
    $('#productSizeGroupId2').change(function () {
        let productSizeGroupId2 = $('#productSizeGroupId2').val();
        $('#config_productSizeGroup2').val(productSizeGroupId2);


    });
    $('#productSizeGroupId3').change(function () {
        let productSizeGroupId3 = $('#productSizeGroupId3').val();
        $('#config_productSizeGroup3').val(productSizeGroupId3);


    });
    $('#productSizeGroupId4').change(function () {
        let productSizeGroupId4 = $('#productSizeGroupId4').val();
        $('#config_productSizeGroup4').val(productSizeGroupId4);


    });
    $('#productSizeGroupId5').change(function () {
        let productSizeGroupId5 = $('#productSizeGroupId5').val();
        $('#config_productSizeGroup5').val(productSizeGroupId5);


    });

});
$(window).on('load', function () {
    if ($("#labelconfig_budgetMonth")) {
        $("#labelconfig_budgetMonth").html("Budget di Spesa Mensile");
    }
    if ($("#labelconfig_productSizeGroup1")) {
        $("#labelconfig_productSizeGroup1").html("Id Gruppo Taglia 1 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroup2")) {
        $("#labelconfig_productSizeGroup2").html("Id Gruppo Taglia 2 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroup3")) {
        $("#labelconfig_productSizeGroup3").html("Id Gruppo Taglia 3 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroup4")) {
        $("#labelconfig_productSizeGroup4").html("Id Gruppo Taglia 4 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_productSizeGroup5")) {
        $("#labelconfig_productSizeGroup5").html("Id Gruppo Taglia 5 Per Cambiare il Gruppo Taglia utilizzare l'apposito selettore a fine Form");
    }
    if ($("#labelconfig_valueexcept1")) {
        $("#labelconfig_valueexcept1").html("Imposta il valore del Moltiplicatore Per il gruppo taglia 1");
    }
    if ($("#labelconfig_valueexcept2")) {
        $("#labelconfig_valueexcept2").html("Imposta il valore del Moltiplicatore Per il gruppo taglia 2");
    }
    if ($("#labelconfig_maxCost")) {
        $("#labelconfig_maxCost").html("Costo Massimo Periodo(maxCos)");
    }
    if ($("#labelconfig_timeRange")) {
        $("#labelconfig_timeRange").html("numero Giorni Periodo di Calcolo");
    }
    if ($("#labelconfig_multiplierDefault")) {
        $("#labelconfig_multiplierDefault").html("Moltiplicatore di Default");
    }
    if ($("#labelconfig_priceModifier")) {
        $("#labelconfig_priceModifier").html("CPC Dedicato");
    }
    if ($("#labelconfig_activeAutomatic")) {
        $("#labelconfig_activeAutomatic").html("Pubblicazione Automatica a inizio Mese");
    }
    if ($("#labelconfig_priceModifierRange1")) {
        $("#labelconfig_priceModifierRange1").html("Fascia di Prezzo 1 CPC Dedicato");
    }
    if ($("#labelconfig_priceModifierRange2")) {
        $("#labelconfig_priceModifierRange2").html("Fascia di Prezzo 2 CPC Dedicato");
    }
    if ($("#labelconfig_priceModifierRange3")) {
        $("#labelconfig_priceModifierRange3").html("Fascia di Prezzo 3 CPC Dedicato");
    }
    if ($("#labelconfig_priceModifierRange4")) {
        $("#labelconfig_priceModifierRange4").html("Fascia di Prezzo 4 CPC Dedicato");
    }
    if ($("#labelconfig_range1Cpc")) {
        $("#labelconfig_range1Cpc").html("CPC Dedicato Fascia 1 in Euro");
    }
    if ($("#labelconfig_range2Cpc")) {
        $("#labelconfig_range2Cpc").html("CPC Dedicato Fascia 2 in Euro");
    }
    if ($("#labelconfig_range3Cpc")) {
        $("#labelconfig_range3Cpc").html("CPC Dedicato Fascia 3 in Euro");
    }
    if ($("#labelconfig_range4Cpc")) {
        $("#labelconfig_range4Cpc").html("CPC Dedicato Fascia 4 in Euro");
    }


});

