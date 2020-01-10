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
                if(key!='productSizeGroupId1'|| key!='productSizeGroupId2' || key!='productSizeGroupId3' || key!='productSizeGroupId4' || key!='productSizeGroupId5' || key!='productSizeGroupEx1' || key!='productSizeGroupEx2' || key!='productSizeGroupEx3' || key!='productSizeGroupEx4' || key!='productSizeGroupEx5' || key!='productSizeGroupEx6' || key!='productCategoryId1' || key!='productCategoryId2' || key!='productCategoryId3' || key!='productCategoryId4' || key!='productCategoryId5' || key!='productCategoryIdEx1' || key!='productCategoryIdEx2' || key!='productCategoryIdEx3' || key!='productCategoryIdEx4' || key!='productCategoryIdEx5'|| key!='productCategoryIdEx6'  ) {
                    config = config + '"' + key + '":' + qualifierTypeText + input.val() + qualifierTypeText + ',';
                }

            }
        });
    config = config.replace(/config_/g, '');
    let n = config.indexOf("productCategoryId5");
    let i =config.indexOf("nameAggregator")-1;
    config= config.substring(i, n + 23);
    config='{'+ config +'}';
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
            nameAggregator:$('#config_nameAggregator').val(),

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

                '<div class="col-md-2" id="div_{{field}}">' +
                //'<div class="col-md-offset-{{offset}} col-md-{{colLength}}">' +
                '<div class="form-group form-group-default required">' +
                '<label for="{{field}}" id="label{{field}}">{{label}}</label>' +
                '<input id="{{field}}" autocomplete="off" type="text" class="form-control" ' +
                'name="{{field}}" value="" required="required"/>' +
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
    var initial=0;


    "use strict";
    if (prefix != '') box.append($('<p>' + prefix + '</p>'));

    for (let prop in object) {



        if(prop == 'nameAggregator' || prop=='activeAutomatic'  || prop=='budgetMonth' || prop=='productSizeGroupEx1') {
            box.append('<div class="row">');
        }

        if( prop=='priceModifierRange1') {
            box.append(' <div id="destination_label_productSizeGroup"></div><div id="destination_group_productSizeGroup"></div><div id="destination_label_productCategoryGroup"></div><div id="destination_group_productCategoryGroup"></div></div><div class="row">Fascia1</div><div class="row">');
        }
        if( prop=='priceModifierRange2') {
            box.append(' <div class="row">Fascia2</div><div class="row">');
        }
        if( prop=='priceModifierRange3') {
            box.append('<div class="row">Fascia3</div><div class="row">');
        }
        if( prop=='priceModifierRange4') {
            box.append('<div class="row">Fascia4</div><div class="row">');
        }
        if( prop=='priceModifierRange5') {
            box.append('<div class="row">Fascia5</div><div class="row">');
        }



        if (object.hasOwnProperty(prop) && typeof object[prop] != 'function') {

            if (typeof object[prop] == 'object' && prefix == '') {

                drawObject(prop, object[prop], inputMock, box, offset);

            } else if (typeof object[prop] == 'object') {

                drawObject(prefix + '_' + prop, object[prop], inputMock, box, offset + 1);
            } else {

                drawInput(prefix, prop, object[prop], inputMock, box, offset + 1);

            }
        }
        if( prop=='emaiNotifyOffline') {
            box.append('</div>');
        }
        if(prop == 'feedUrl' || prop=='priceModifier' || prop=='productSizeGroupEx6'|| prop=='productCategoryIdEx6' ) {
            box.append('</div>');
        }
        if( prop=='maxCos1' ) {
            box.append('<div id="destination_productGroupId1"></div><div id="destination_productSizeGroupEx1"></div></div>');
        }
        if(  prop=='maxCos2') {
            box.append('<div id="destination_productGroupId2"></div><div id="destination_productSizeGroupEx2"></div></div>');
        }
        if(  prop=='maxCos3' ) {
            box.append('<div id="destination_productGroupId3"></div><div id="destination_productSizeGroupEx3"></div></div>');
        }
        if(  prop=='maxCos4' ) {
            box.append('<div id="destination_productGroupId4"></div><div id="destination_productSizeGroupEx4"></div></div>');
        }
        if( prop=='maxCos5') {
            box.append('<div id="destination_productGroupId5"></div><div id="destination_productSizeGroupEx5"></div></div>');
        }


        initial++;
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

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'ProductSizeGroup',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#productSizeGroupEx1');
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
        var select = $('#productSizeGroupEx2');
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
        var select = $('#productSizeGroupEx3');
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
        var select = $('#productSizeGroupEx4');
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
        var select = $('#productSizeGroupEx5');
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
        var select = $('#productSizeGroupEx6');
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
    $('#productSizeGroupEx1').change(function () {
        let productSizeGroupEx1 = $('#productSizeGroupEx1').val();
        $('#config_productSizeGroupEx1').val(productSizeGroupEx1);
    });
    $('#productSizeGroupEx2').change(function () {
        let productSizeGroupEx2 = $('#productSizeGroupEx2').val();
        $('#config_productSizeGroupEx2').val(productSizeGroupEx2);
    });
    $('#productSizeGroupEx3').change(function () {
        let productSizeGroupEx3 = $('#productSizeGroupEx3').val();
        $('#config_productSizeGroupEx3').val(productSizeGroupEx3);
    });
    $('#productSizeGroupEx4').change(function () {
        let productSizeGroupEx4 = $('#productSizeGroupEx4').val();
        $('#config_productSizeGroupEx4').val(productSizeGroupEx4);
    });
    $('#productSizeGroupEx5').change(function () {
        let productSizeGroupEx5 = $('#productSizeGroupEx5').val();
        $('#config_productSizeGroupEx5').val(productSizeGroupEx5);
    });
    $('#productSizeGroupEx6').change(function () {
        let productSizeGroupEx6 = $('#productSizeGroupEx6').val();
        $('#config_productSizeGroupEx6').val(productSizeGroupEx6);
    });




    $('#productCategoryId1').change(function () {
        let productCategoryId1 = $('#productCategoryId1').val();
        $('#config_productCategoryId1').val(productCategoryId1);
    });
    $('#productCategoryId2').change(function () {
        let productCategoryId2 = $('#productCategoryId2').val();
        $('#config_productCategoryId2').val(productCategoryId2);
    });
    $('#productCategoryId3').change(function () {
        let productCategoryId3 = $('#productCategoryId3').val();
        $('#config_productCategoryId3').val(productCategoryId3);
    });
    $('#productCategoryId4').change(function () {

        let productCategoryId4 = $('#productCategoryId4').val();
        $('#config_productCategoryId4').val(productCategoryId4);
    });
    $('#productCategoryId5').change(function () {

        let productCategoryId5 = $('#productCategoryId5').val();
        $('#config_productCategoryId5').val(productCategoryId5);
    });
    $('#productCategoryIdEx1').change(function () {
        let productCategoryIdEx1 = $('#productCategoryIdEx1').val();
        $('#config_productCategoryIdEx1').val(productCategoryIdEx1);
    });
    $('#productCategoryIdEx2').change(function () {
        let productCategoryIdEx2 = $('#productCategoryIdEx2').val();
        $('#config_productCategoryIdEx2').val(productCategoryIdEx2);
    });
    $('#productCategoryIdEx3').change(function () {
        let productCategoryIdEx3 = $('#productCategoryIdEx3').val();
        $('#config_productCategoryIdEx3').val(productCategoryIdEx3);
    });
    $('#productCategoryIdEx4').change(function () {
        let productCategoryIdEx4 = $('#productCategoryIdEx4').val();
        $('#config_productCategoryIdEx4').val(productCategoryIdEx4);
    });
    $('#productCategoryIdEx5').change(function () {
        let productCategoryIdEx5 = $('#productCategoryIdEx5').val();
        $('#config_productCategoryIdEx5').val(productCategoryIdEx5);
    });
    $('#productCategoryIdEx6').change(function () {
        let productCategoryIdEx6 = $('#productCategoryIdEx6').val();
        $('#config_productCategoryIdEx6').val(productCategoryIdEx6);
    });
});
$(window).on('load', function () {
    if ($("#labelconfig_budgetMonth")) {
        $("#labelconfig_budgetMonth").html("Budget di Spesa Mensile");
    }
    if ($("#labelconfig_productSizeGroup1")) {
        $("#labelconfig_productSizeGroup1").html("Id Gruppo Taglia 1 ");
    }
    if ($("#labelconfig_productSizeGroup2")) {
        $("#labelconfig_productSizeGroup2").html("Id Gruppo Taglia 2 ");
    }
    if ($("#labelconfig_productSizeGroup3")) {
        $("#labelconfig_productSizeGroup3").html("Id Gruppo Taglia 3 ");
    }
    if ($("#labelconfig_productSizeGroup4")) {
        $("#labelconfig_productSizeGroup4").html("Id Gruppo Taglia 4 ");
    }
    if ($("#labelconfig_productSizeGroup5")) {
        $("#labelconfig_productSizeGroup5").html("Id Gruppo Taglia 5 P");
    }
    if ($("#labelconfig_productSizeGroupEx1")) {
        $("#labelconfig_productSizeGroupEx1").html("Id Esclusione Gr. Taglia 1 ");
    }
    if ($("#labelconfig_productSizeGroupEx2")) {
        $("#labelconfig_productSizeGroupEx2").html("Id Esclusione Gr. Taglia 2 ");
    }
    if ($("#labelconfig_productSizeGroupEx3")) {
        $("#labelconfig_productSizeGroupEx3").html("Id Esclusione Gr. Taglia 3 ");
    }
    if ($("#labelconfig_productSizeGroupEx4")) {
        $("#labelconfig_productSizeGroupEx4").html("Id Esclusione Gr. Taglia 4 ");
    }
    if ($("#labelconfig_productSizeGroupEx5")) {
        $("#labelconfig_productSizeGroupEx5").html("Id Esclusione Gr. Taglia 5 ");
    }
    if ($("#labelconfig_productSizeGroupEx6")) {
        $("#labelconfig_productSizeGroupEx6").html("Id Esclusione Gr. Taglia 6 ");
    }
    //inizio categorie
    if ($("#labelconfig_productCategoryId1")) {
        $("#labelconfig_productCategoryId1").html("Id Categoria 1 ");
    }
    if ($("#labelconfig_productCategoryId2")) {
        $("#labelconfig_productCategoryId2").html("Id Categoria 2 ");
    }
    if ($("#labelconfig_productCategoryId3")) {
        $("#labelconfig_productCategoryId3").html("Id Categoria 3 ");
    }
    if ($("#labelconfig_productCategoryId4")) {
        $("#labelconfig_productCategoryId4").html("Id Categoria 4 ");
    }
    if ($("#labelconfig_productCategoryId5")) {
        $("#labelconfig_productCategoryId5").html("Id Categoria 5 ");
    }
    if ($("#labelconfig_productCategoryIdEx1")) {
        $("#labelconfig_productCategoryIdEx1").html("Id Esclusione Categoria 1 ");
    }
    if ($("#labelconfig_productCategoryIdEx2")) {
        $("#labelconfig_productCategoryIdEx2").html("Id Esclusione Categoria 2 ");
    }
    if ($("#labelconfig_productCategoryIdEx3")) {
        $("#labelconfig_productCategoryIdEx3").html("Id Esclusione Categoria 3 ");
    }
    if ($("#labelconfig_productCategoryIdEx4")) {
        $("#labelconfig_productCategoryIdEx4").html("Id Esclusione Categoria 4 ");
    }
    if ($("#labelconfig_productCategoryIdEx5")) {
        $("#labelconfig_productCategoryIdEx5").html("Id Esclusione Categoria 5 ");
    }
    if ($("#labelconfig_productCategoryIdEx6")) {
        $("#labelconfig_productCategoryIdEx6").html("Id Esclusione Categoria 6");
    }





    //fine categorie
    if ($("#labelconfig_valueexcept1")) {
        $("#labelconfig_valueexcept1").html("Moltiplicatore 1");
    }
    if ($("#labelconfig_valueexcept2")) {
        $("#labelconfig_valueexcept2").html("Moltiplicatore 2");
    }
    if ($("#labelconfig_valueexcept3")) {
        $("#labelconfig_valueexcept3").html("Moltiplicatore 3");
    }
    if ($("#labelconfig_valueexcept4")) {
        $("#labelconfig_valueexcept4").html("Moltiplicatore 4");
    }
    if ($("#labelconfig_valueexcept5")) {
        $("#labelconfig_valueexcept5").html("Moltiplicatore 5");
    }
    if ($("#labelconfig_maxCos1")) {
        $("#labelconfig_maxCos1").html("MaxCos 1");
    }
    if ($("#labelconfig_maxCos2")) {
        $("#labelconfig_maxCos2").html("MaxCos 2");
    }
    if ($("#labelconfig_maxCos3")) {
        $("#labelconfig_maxCos3").html("MaxCos 3");
    }
    if ($("#labelconfig_maxCos4")) {
        $("#labelconfig_maxCos4").html("MaxCos 4");
    }
    if ($("#labelconfig_maxCos5")) {
        $("#labelconfig_maxCos5").html("MaxCos 5");
    }
    if ($("#labelconfig_timeRange")) {
        $("#labelconfig_timeRange").html("GG di Calco Periodo");
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
        $("#labelconfig_priceModifierRange1").html("Range Retail Price 1");
    }
    if ($("#labelconfig_priceModifierRange2")) {
        $("#labelconfig_priceModifierRange2").html("Range Retail Price 2");
    }
    if ($("#labelconfig_priceModifierRange3")) {
        $("#labelconfig_priceModifierRange3").html("Range Retail Price 3");
    }
    if ($("#labelconfig_priceModifierRange4")) {
        $("#labelconfig_priceModifierRange4").html("Range Retail Price 4");
    }
    if ($("#labelconfig_priceModifierRange5")) {
        $("#labelconfig_priceModifierRange5").html("Range Retail Price 5");
    }
    if ($("#labelconfig_range1Cpc")) {
        $("#labelconfig_range1Cpc").html("CPC Dedicato 1");
    }
    if ($("#labelconfig_range2Cpc")) {
        $("#labelconfig_range2Cpc").html("CPC Dedicato  2");
    }
    if ($("#labelconfig_range3Cpc")) {
        $("#labelconfig_range3Cpc").html("CPC Dedicato 3");
    }
    if ($("#labelconfig_range4Cpc")) {
        $("#labelconfig_range4Cpc").html("CPC Dedicato 4");
    }
    if ($("#labelconfig_range5Cpc")) {
        $("#labelconfig_range5Cpc").html("CPC Dedicato 5");
    }
    if ($("#labelconfig_emailDepublish")) {
        $("#labelconfig_emailDepublish").html("Email Notifica Prodotti Depubblicati");
    }
    if ($("#labelconfig_emaiNotifyOffline")) {
        $("#labelconfig_emaiNotifyOffline").html("Email Notifica  OffLine");
    }
    if ($("#labelconfig_nameAggegator")) {
        $("#labelconfig_nameAggregator").html("Nome Aggregatore");
    }
    if ($("#labelconfig_lang")) {
        $("#labelconfig_lang").html("Lingua");
    }
    if($("#div_config_defaultCpc")){
        $("#div_config_defaultCpc").addClass('hidden');
    }
    if($("#div_config_defaultCpcM")){
        $("#div_config_defaultCpcM").addClass('hidden');
    }
    if($("#div_config_defaultCpcF")){
        $("#div_config_defaultCpcF").addClass('hidden');
    }
    if($("#div_config_defaultCpcFMobile")){
        $("#div_config_defaultCpcFMobile").addClass('hidden');
    }
    if($("#div_config_priceModifier")){
        $("#div_config_priceModifier").addClass('hidden');
    }
    if($("#div_config_productCategoryIdEx1")){
        $("#div_config_productCategoryIdEx1").addClass('hidden');
    }
    if($("#div_config_productCategoryIdEx2")){
        $("#div_config_productCategoryIdEx2").addClass('hidden');
    }
    if($("#div_config_productCategoryIdEx3")){
        $("#div_config_productCategoryIdEx3").addClass('hidden');
    }
    if($("#div_config_productCategoryIdEx4")){
        $("#div_config_productCategoryIdEx4").addClass('hidden');
    }
    if($("#div_config_productCategoryIdEx5")){
        $("#div_config_productCategoryIdEx5").addClass('hidden');
    }
    if($("#div_config_productCategoryIdEx6")){
        $("#div_config_productCategoryIdEx6").addClass('hidden');
    }
    if($("#div_config_productSizeGroupEx1")){
        $("#div_config_productSizeGroupEx1").addClass('hidden');
    }
    if($("#div_config_productSizeGroupEx2")){
        $("#div_config_productSizeGroupEx2").addClass('hidden');
    }
    if($("#div_config_productSizeGroupEx3")){
        $("#div_config_productSizeGroupEx3").addClass('hidden');
    }
    if($("#div_config_productSizeGroupEx4")){
        $("#div_config_productSizeGroupEx4").addClass('hidden');
    }
    if($("#div_config_productSizeGroupEx5")){
        $("#div_config_productSizeGroupEx5").addClass('hidden');
    }
    if($("#div_config_productSizeGroupEx6")){
        $("#div_config_productSizeGroupEx6").addClass('hidden');
    }
    if($("#div_config_productCategoryIdEx1")){
        $("#div_config_productCategoryIdEx1").addClass('hidden');
    }
    if($("#div_config_range1Cpc")){
        $("#div_config_range1Cpc").addClass('hidden');
    }
    if($("#div_config_range2Cpc")){
        $("#div_config_range2Cpc").addClass('hidden');
    }
    if($("#div_config_range3Cpc")){
        $("#div_config_range3Cpc").addClass('hidden');
    }
    if($("#div_config_range4Cpc")){
        $("#div_config_range4Cpc").addClass('hidden');
    }
    if($("#div_config_range5Cpc")){
        $("#div_config_range5Cpc").addClass('hidden');
    }
    if($("#div_config_productSizeGroup1")){
        $("#div_config_productSizeGroup1").addClass('hidden');
    }
    if($("#div_config_productSizeGroup2")){
        $("#div_config_productSizeGroup2").addClass('hidden');
    }
    if($("#div_config_productSizeGroup3")){
        $("#div_config_productSizeGroup3").addClass('hidden');
    }
    if($("#div_config_productSizeGroup4")){
        $("#div_config_productSizeGroup4").addClass('hidden');
    }
    if($("#div_config_productSizeGroup5")){
        $("#div_config_productSizeGroup5").addClass('hidden');
    }
    if($("#div_config_productCategoryId1")){
        $("#div_config_productCategoryId1").addClass('hidden');
    }
    if($("#div_config_productCategoryId2")){
        $("#div_config_productCategoryId2").addClass('hidden');
    }
    if($("#div_config_productCategoryId3")){
        $("#div_config_productCategoryId3").addClass('hidden');
    }
    if($("#div_config_productCategoryId4")){
        $("#div_config_productCategoryId4").addClass('hidden');
    }
    if($("#div_config_productCategoryId5")){
        $("#div_config_productCategoryId5").addClass('hidden');
    }
    if($("#div_config_maxCos1")){
        $("#div_config_maxCos1").removeClass('col-md-2');
        $("#div_config_maxCos1").addClass('col-md-2');
    }
    if($("#div_config_maxCos2")){
        $("#div_config_maxCos2").removeClass('col-md-2');
        $("#div_config_maxCos2").addClass('col-md-2');
    }
    if($("#div_config_maxCos3")){
        $("#div_config_maxCos3").removeClass('col-md-2');
        $("#div_config_maxCos3").addClass('col-md-2');
    }
    if($("#div_config_maxCos4")){
        $("#div_config_maxCos4").removeClass('col-md-2');
        $("#div_config_maxCos4").addClass('col-md-2');
    }
    if($("#div_config_maxCos5")){
        $("#div_config_maxCos5").removeClass('col-md-2');
        $("#div_config_maxCos5").addClass('col-md-2');
    }
    $("#source_group_productSizeGroup").detach().appendTo('#destination_group_productSizeGroup');
    $("#source_label_productSizeGroup").detach().appendTo('#destination_label_productSizeGroup');
    $("#source_group_productCategoryGroup").detach().appendTo('#destination_group_productCategoryGroup');
    $("#source_label_productCategoryGroup").detach().appendTo('#destination_label_productCategoryGroup');
    $("#source_productGroupId1").detach().appendTo('#destination_productGroupId1');
    $("#source_productGroupId2").detach().appendTo('#destination_productGroupId2');
    $("#source_productGroupId3").detach().appendTo('#destination_productGroupId3');
    $("#source_productGroupId4").detach().appendTo('#destination_productGroupId4');
    $("#source_productGroupId5").detach().appendTo('#destination_productGroupId5');
    $("#source_productSizeGroupId1").detach().appendTo('#destination_productSizeGroupEx1');
    $("#source_productSizeGroupId2").detach().appendTo('#destination_productSizeGroupEx2');
    $("#source_productSizeGroupId3").detach().appendTo('#destination_productSizeGroupEx3');
    $("#source_productSizeGroupId4").detach().appendTo('#destination_productSizeGroupEx4');
    $("#source_productSizeGroupId5").detach().appendTo('#destination_productSizeGroupEx5');


});

