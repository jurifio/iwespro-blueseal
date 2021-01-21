$(document).ready(function () {

    document.getElementById('insertAggregator').style.display = "block";
    $('#uploadLogo').click(function () {
        let bsModal = $('#bsModal');

        let header = bsModal.find('.modal-header h4');
        let body = bsModal.find('.modal-body');
        let cancelButton = bsModal.find('.modal-footer .btn-default');
        let okButton = bsModal.find('.modal-footer .btn-success');

        bsModal.modal();

        header.html('Carica Foto');
        okButton.html('Fatto').off().on('click', function () {
            bsModal.modal('hide');
            okButton.off();
        });
        cancelButton.remove();
        let bodyContent =
            '<form id="dropzoneModal" class="dropzone" enctype="multipart/form-data" name="dropzonePhoto" action="POST">' +
            '<div class="fallback">' +
            '<input name="file" type="file" multiple />' +
            '</div>' +
            '</form>';

        body.html(bodyContent);
        let dropzone = new Dropzone("#dropzoneModal", {
            url: "/blueseal/xhr/UploadAggregatorImageAjaxController",
            maxFilesize: 5,
            maxFiles: 100,
            parallelUploads: 10,
            acceptedFiles: "image/*",
            dictDefaultMessage: "Trascina qui i file da inviare o clicca qui",
            uploadMultiple: true,
            sending: function (file, xhr, formData) {
            },
            success: function (res) {
                $('#returnFileLogo').append('<img src="https://iwes.s3.amazonaws.com/iwes-aggregator/' + res['name'] + '">');
                $('#logoFile').val('https://iwes.s3.amazonaws.com/iwes-aggregator/' + res['name']);
            }
        });

        dropzone.on('addedfile', function () {
            okButton.attr("disabled", "disabled");
        });
        dropzone.on('queuecomplete', function () {
            okButton.removeAttr("disabled");
            $(document).trigger('bs.load.photo');
        });
    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'AggregatorHasShop',
            condition: {isActive: 1}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#aggregatorHasShopId');
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
            table: 'Shop',
            condition: {hasEcommerce: 1}

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#shopId');
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
            table: 'Lang',

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#lang');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'lang',
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


});





$('#aggregatorHasShopId').change(function () {
    $('#rawBrands').empty();
    var shopSelect = $('#aggregatorHasShopId').val();
    $.ajax({
        url: '/blueseal/xhr/SelectBrandMarketplaceAccountAjaxController',
        method: 'get',
        data: {
            aggregatorHasShopId: shopSelect
        },
        dataType: 'json'
    }).done(function (res) {
        console.log(res);
        let rawBrands = res;
        let bodyres;
        bodyres='<div class="row"><div class="col-md-4"><input type="text" id="myInput" onkeyup="myFunction()" placeholder="ricerca Brand"></div>';
        bodyres= bodyres+'<div class="col-md-4"><input type="text" id="myShop" onkeyup="myShopFunction()" placeholder="ricerca per Shop"></div>';
        bodyres= bodyres+'<div class="col-md-4"><input type="checkbox" class="form-control"  id="checkedAll" name="checkedAll"></div></div>';
        bodyres = bodyres+'<table id="myTable"> <tr class="header1"><th style="width:40%;">Categoria</th><th style="width:40%;">Shop</th><th style="width:20%;">Selezione</th></tr>';
      //  $('#rawBrands').append('<input type="text" id="myInput" onkeyup="myFunction()" placeholder="ricerca Brand"/>');
       // $('#rawBrands').append('<table id="myTable"> <tr class="header1"><th style="width:20%;">Categoria</th><th style="width:20%;">Shop</th><th style="width:20%;">Shop Id Origine</th><th style="width:20%;">Shop Id Origine</th><th style="width:20%;">Shop Id Destinazione</th><th style="width:20%;">Pubblicato</th></tr>');
      ////  $('#rawBrands').append('<div class="row"><div class="col-md-2">id Categoria</div><div class="col-md-2">Categoria</div><div class="col-md-2">ShopName</div>' + '<div class="col-md-2">Shop Id Origine</div>' + '<div class="col-md-2">Shop Id Destinazione</div><div class="col-md-2">Pubblicato</div></div>');
        $.each(rawBrands, function (k, v) {
            bodyres=bodyres+'<tr><td style="width:40%;">' + v.brandName + '</td><td style="width:40%;">' + v.shopName + '</td><td style="width:20%;"><input type="checkbox" class="form-control"  name="selected_values[]" value="'+v.id+'-'+v.shopIdOrigin+'"></td></tr>';
           // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
        });
        bodyres=bodyres+'</table>';
        $('#rawBrands').append(bodyres);
    });




});

$('#selectCreationCampaign').change(function () {
    if ($('#selectCreationCampaign').val() == 1) {
        $('#divcampaign').empty();
        $('#divcampaign').append(`
         <div class="col-md-12">
                                        <div class="form-group form-group-default required">
                                            <input type="hidden" id="typeInsertionCampaign" name="typeInsertionCampaign" value="1"/>
                                            <label for="campaignName">Nome campagna</label>
                                            <input id="campaignName" autocomplete="off" type="text"
                                                   class="form-control" name="campaignName" value=""
                                                   required="required"/>
                                            <span class="bs red corner label"><i
                                                        class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>
        
        `);
    } else {
        $('#divcampaign').empty();
        $('#divcampaign').append(`
         <div class="col-md-12">
                                        <div class="form-group form-group-default required">
                                         <input type="hidden" id="typeInsertionCampaign" name="typeInsertionCampaign" value="2"/>
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="campaignName">Nome Campagna
                                                </label>
                                                <select id="campaignName"
                                                        name="campaignName"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione la Campagna"
                                                        data-init-plugin="selectize">
                                                </select>
                                                <span class="bs red corner label"><i
                                                            class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                    </div>
        `);
        $.ajax({
            method: 'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Campaign'
            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#campaignName');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                options: res2,
            });

        });
    }
});



$(document).on('bs.marketplace-account.save', function () {
    let bsModal = new $.bsModal('Inserimento Aggregatore', {
        body: '<p>Confermare?</p>'
    });

    var val='';
    $(':checkbox:checked').each(function(i){
        if($(this)!=$('#checkedAll')) {
            val = val + $(this).val() + ',';
        }
    });
    var lang = $('#lang').val();
    var marketplace_account_name = $('#marketplace_account_name').val();
    var slug = $('#slug').val();
    var useRange = $('#useRange').val();
    var aggregatorHasShopId = $('#aggregatorHasShopId').val();
    var nameAdminister = $('#nameAdminister').val();
    var emailNotify = $('#emailNotify').val();
    var isActive = $('#isActive').val();
    var isActiveDepublisher=$('#isActiveDepublisher').val();
    var defaultCpcF = $('#defaultCpcF').val();
    var defaultCpcFM = $('#defaultCpcFM').val();
    var logoFile = $('#logoFile').val();
    var defaultCpcM = $('#defaultCpcM').val();
    var defaultCpc = $('#defaultCpc').val();
    var budget01 = $('#budget01').val();
    var budget02 = $('#budget02').val();
    var budget03 = $('#budget03').val();
    var budget04 = $('#budget04').val();
    var budget05 = $('#budget05').val();
    var budget06 = $('#budget06').val();
    var budget07 = $('#budget07').val();
    var budget08 = $('#budget08').val();
    var budget09 = $('#budget09').val();
    var budget10 = $('#budget10').val();
    var budget11 = $('#budget11').val();
    var budget12 = $('#budget12').val();
    var typeInsertion = $('#typeInsertion').val();
    var marketplaceName = $('#marketplaceName').val();
    var campaignName=$('#campaignName').val();
    var typeInsertionCampaign=$('#typeInsertionCampaign').val();
    var productCategoryIdEx1 = $('#productCategoryIdEx1').val();
    var productCategoryIdEx2 = $('#productCategoryIdEx2').val();
    var productCategoryIdEx3 = $('#productCategoryIdEx3').val();
    var productCategoryIdEx4 = $('#productCategoryIdEx4').val();
    var productCategoryIdEx5 = $('#productCategoryIdEx5').val();
    var productCategoryIdEx6 = $('#productCategoryIdEx6').val();
    var productSizeGroupEx1 = $('#productSizeGroupEx1').val();
    var productSizeGroupEx2 = $('#productSizeGroupEx2').val();
    var productSizeGroupEx3 = $('#productSizeGroupEx3').val();
    var productSizeGroupEx4 = $('#productSizeGroupEx4').val();
    var productSizeGroupEx5 = $('#productSizeGroupEx5').val();
    var productSizeGroupEx6 = $('#productSizeGroupEx6').val();
    var priceModifierRange1 = $('#priceModifierRange1').val();
    var priceModifierRange2 = $('#priceModifierRange2').val();
    var priceModifierRange3 = $('#priceModifierRange3').val();
    var priceModifierRange4 = $('#priceModifierRange4').val();
    var priceModifierRange5 = $('#priceModifierRange5').val();
    var range1Cpc = $('#range1Cpc').val();
    var range2Cpc = $('#range2Cpc').val();
    var range3Cpc = $('#range3Cpc').val();
    var range4Cpc = $('#range4Cpc').val();
    var range5Cpc = $('#range5Cpc').val();
    var range1CpcM = $('#range1CpcM').val();
    var range2CpcM = $('#range2CpcM').val();
    var range3CpcM = $('#range3CpcM').val();
    var range4CpcM = $('#range4CpcM').val();
    var range5CpcM = $('#range5CpcM').val();
    var productSizeGroupId1 = $('#productSizeGroupId1').val();
    var productSizeGroupId2 = $('#productSizeGroupId2').val();
    var productSizeGroupId3 = $('#productSizeGroupId3').val();
    var productSizeGroupId4 = $('#productSizeGroupId4').val();
    var productSizeGroupId5 = $('#productSizeGroupId5').val();
    var productCategoryId1 = $('#productCategoryId1').val();
    var productCategoryId2 = $('#productCategoryId2').val();
    var productCategoryId3 = $('#productCategoryId3').val();
    var productCategoryId4 = $('#productCategoryId4').val();
    var productCategoryId5 = $('#productCategoryId5').val();
    var nameRule = $('#nameRule').val();

    /* var config = {
         lang: $('#lang').val(),
         marketplace_account_name: $('#marketplace_account_name').val(),
         slug: $('#slug').val(),
         nameAdminister: $('#nameAdminister').val(),
         emailNotify: $('#emailNotify').val(),
         isActive: $('#isActive').val(),
         defaultCpcF: $('#defaultCpcF').val(),
         defaultCpcFM: $('#defaultCpcFM').val(),
         logoFile: $('#logoFile').val(),
         defaultCpcM: $('#defaultCpcM').val(),
         defaultCpc: $('#defaultCpc').val(),
         budget01: $('#budget01').val(),
         budget02: $('#budget02').val(),
         budget03: $('#budget03').val(),
         budget04: $('#budget04').val(),
         budget05: $('#budget05').val(),
         budget06: $('#budget06').val(),
         budget07: $('#budget07').val(),
         budget08: $('#budget08').val(),
         budget09: $('#budget09').val(),
         budget10: $('#budget10').val(),
         budget11: $('#budget11').val(),
         budget12: $('#budget12').val(),
         typeInsertion: $('#typeInsertion').val(),
         marketplaceName: $('#marketplaceName'),
         productCategoryIdEx1: $('#productCategoryIdEx1').val(),
         productCategoryIdEx2: $('#productCategoryIdEx2').val(),
         productCategoryIdEx3: $('#productCategoryIdEx3').val(),
         productCategoryIdEx4: $('#productCategoryIdEx4').val(),
         productCategoryIdEx5: $('#productCategoryIdEx5').val(),
         productCategoryIdEx6: $('#productCategoryIdEx6').val(),
         productSizeGroupEx1: $('#productSizeGroupEx1').val(),
         productSizeGroupEx2: $('#productSizeGroupEx2').val(),
         productSizeGroupEx3: $('#productSizeGroupEx3').val(),
         productSizeGroupEx4: $('#productSizeGroupEx4').val(),
         productSizeGroupEx5: $('#productSizeGroupEx5').val(),
         productSizeGroupEx6: $('#productSizeGroupEx6').val(),
         priceModifierRange1: $('#priceModifierRange1').val(),
         priceModifierRange2: $('#priceModifierRange2').val(),
         priceModifierRange3: $('#priceModifierRange3').val(),
         priceModifierRange4: $('#priceModifierRange4').val(),
         priceModifierRange5: $('#priceModifierRange5').val(),
         range1Cpc: $('#range1Cpc').val(),
         range2Cpc: $('#range2Cpc').val(),
         range3Cpc: $('#range3Cpc').val(),
         range4Cpc: $('#range4Cpc').val(),
         range5Cpc: $('#range5Cpc').val(),
         productSizeGroupId1: $('#productSizeGroupId1').val(),
         productSizeGroupId2: $('#productSizeGroupId2').val(),
         productSizeGroupId3: $('#productSizeGroupId3').val(),
         productSizeGroupId4: $('#productSizeGroupId4').val(),
         productSizeGroupId5: $('#productSizeGroupId5').val(),
         productCategoryId1: $('#productCategoryId1').val(),
         productCategoryId2: $('#productCategoryId2').val(),
         productCategoryId3: $('#productCategoryId3').val(),
         productCategoryId4: $('#productCategoryId4').val(),
         productCategoryId5: $('#productCategoryId5').val()*/

    var config = '?nameAggregator=' + marketplace_account_name + '&' +
        'typeInsertion=' + typeInsertion + '&' +
        'marketplaceName=' + marketplaceName + '&' +
        'typeInsertionCampaign=' + typeInsertionCampaign + '&' +
        'campaignName=' + campaignName + '&' +
        'lang=' + lang + '&' +
        'aggregatorHasShopId=' + aggregatorHasShopId + '&' +
        'slug=' + slug + '&' +
        'logoFile=' + logoFile + '&' +
        'activeAutomatic=' + useRange + '&' +
        'isActive=' + isActive + '&' +
        'isActiveDepublisher=' + isActiveDepublisher + '&' +
        'defaultCpc=' + defaultCpc + '&' +
        'defaultCpcM=' + defaultCpcM + '&' +
        'defaultCpcF=' + defaultCpcF + '&' +
        'defaultCpcFM=' + defaultCpcFM + '&' +
        'budget01=' + budget01 + '&' +
        'budget02=' + budget02 + '&' +
        'budget03=' + budget03 + '&' +
        'budget04=' + budget04 + '&' +
        'budget05=' + budget05 + '&' +
        'budget06=' + budget06 + '&' +
        'budget07=' + budget07 + '&' +
        'budget08=' + budget08 + '&' +
        'budget09=' + budget09 + '&' +
        'budget10=' + budget10 + '&' +
        'budget11=' + budget11 + '&' +
        'budget12=' + budget12 + '&' +
        'nameAdminister=' + nameAdminister + '&' +
        'emailNotify=' + emailNotify + '&' +
        'productSizeGroupEx1=' + productSizeGroupEx1 + '&' +
        'productSizeGroupEx2=' + productSizeGroupEx2 + '&' +
        'productSizeGroupEx3=' + productSizeGroupEx3 + '&' +
        'productSizeGroupEx4=' + productSizeGroupEx4 + '&' +
        'productSizeGroupEx5=' + productSizeGroupEx5 + '&' +
        'productSizeGroupEx6=' + productSizeGroupEx6 + '&' +
        'productCategoryIdEx1=' + productCategoryIdEx1 + '&' +
        'productCategoryIdEx2=' + productCategoryIdEx2 + '&' +
        'productCategoryIdEx3=' + productCategoryIdEx3 + '&' +
        'productCategoryIdEx4=' + productCategoryIdEx4 + '&' +
        'productCategoryIdEx5=' + productCategoryIdEx5 + '&' +
        'productCategoryIdEx6=' + productCategoryIdEx6 + '&' +
        'priceModifierRange1=' + priceModifierRange1 + '&' +
        'range1Cpc=' + range1Cpc + '&' +
        'range1CpcM=' + range1CpcM + '&' +
        'productSizeGroupId1=' + productSizeGroupId1 + '&' +
        'productCategoryId1=' + productCategoryId1 + '&' +
        'priceModifierRange2=' + priceModifierRange2 + '&' +
        'range2Cpc=' + range2Cpc + '&' +
        'range2CpcM=' + range2CpcM + '&' +
        'productSizeGroupId2=' + productSizeGroupId2 + '&' +
        'productCategoryId2=' + productCategoryId2 + '&' +
        'priceModifierRange3=' + priceModifierRange3 + '&' +
        'range3Cpc=' + range3Cpc + '&' +
        'range3CpcM=' + range3CpcM + '&' +
        'productSizeGroupId3=' + productSizeGroupId3 + '&' +
        'productCategoryId3=' + productCategoryId3 + '&' +
        'priceModifierRange4=' + priceModifierRange4 + '&' +
        'range4Cpc=' + range4Cpc + '&' +
        'range4CpcM=' + range4CpcM + '&' +
        'productSizeGroupId4=' + productSizeGroupId4 + '&' +
        'productCategoryId4=' + productCategoryId4 + '&' +
        'priceModifierRange5=' + priceModifierRange5 + '&' +
        'range5Cpc=' + range5Cpc + '&' +
        'range5CpcM=' + range5CpcM + '&' +
        'productSizeGroupId5=' + productSizeGroupId5 + '&' +
        'productCategoryId5=' + productCategoryId5+ '&' +
        'nameRule='+nameRule+ '&' +
        'ruleOption='+val.substring(0, val.length - 1);
        /*/var config='{"nameAggregator":"'+marketplace_account_name+'",'+
             '"lang":" ' + lang + '",'+
             '"slug":"'+slug+'",'+
             '"logoFile":"'+logoFile+'",'+
             '"activeAutomatic":'+isActive+','+
             '"defaultCpc":'+defaultCpc+','+
             '"defaultCpcM":'+defaultCpcM+','+
             '"defaultCpcF":'+defaultCpcF+','+
             '"defaultCpcFM":'+defaultCpcFM+','+
             '"budget01":'+budget01+' ,'+
             '"budget02":'+budget02+' ,'+
             '"budget03":'+budget03+' ,'+
             '"budget04":'+budget04+' ,'+
             '"budget05":'+budget05+' ,'+
             '"budget06":'+budget06+' ,'+
             '"budget07":'+budget07+' ,'+
             '"budget08":'+budget08+' ,'+
             '"budget09":'+budget09+' ,'+
             '"budget10":'+budget10+' ,'+
             '"budget11":'+budget11+' ,'+
             '"budget12":'+budget12+' ,'+
             '"nameAdminister":"'+nameAdminister+'",'+
             '"emailNotify":"'+emailNotify+'",'+
             '"productSizeGroupEx1":'+productSizeGroupEx1+','+
             '"productSizeGroupEx2":'+productSizeGroupEx2+','+
             '"productSizeGroupEx3":'+productSizeGroupEx3+','+
             '"productSizeGroupEx4":'+productSizeGroupEx4+','+
             '"productSizeGroupEx5":'+productSizeGroupEx5+','+
             '"productSizeGroupEx6":'+productSizeGroupEx6+','+
             '"productCategoryIdEx1":'+productCategoryIdEx1+','+
             '"productCategoryIdEx2":'+productCategoryIdEx2 +','+
             '"productCategoryIdEx3":'+productCategoryIdEx3 +','+
             '"productCategoryIdEx4":'+productCategoryIdEx4 +','+
             '"productCategoryIdEx5":'+productCategoryIdEx5 +','+
             '"productCategoryIdEx6":'+productCategoryIdEx6 +','+
             '"priceModifierRange1":"'+priceModifierRange1 +'",'+
             '"range1Cpc":'+range1Cpc +','+
             '"productSizeGroupId1":'+productSizeGroupId1 +','+
             '"productCategoryId1":'+productCategoryId1 +','+
             '"priceModifierRange2":"'+priceModifierRange2 +'",'+
             '"range2Cpc":'+range2Cpc +','+
             '"productSizeGroupId2":'+productSizeGroupId2 +','+
             '"productCategoryId2":'+productCategoryId2 +','+
             '"priceModifierRange3":"'+priceModifierRange3 +'",'+
             '"range3Cpc":'+range3Cpc +','+
             '"productSizeGroupId3":'+productSizeGroupId3 +','+
             '"productCategoryId3":'+productCategoryId3 +','+
             '"priceModifierRange4":"'+priceModifierRange4+'",'+
             '"range4Cpc":'+range4Cpc+','+
             '"productSizeGroupId4":'+productSizeGroupId4+','+
             '"productCategoryId4":'+productCategoryId4 +','+
             '"priceModifierRange5":"'+priceModifierRange5 +'",'+
             '"range5Cpc":'+range5Cpc+','+
             '"productSizeGroupId5":'+productSizeGroupId5+','+
             '"productCategoryId5":'+productCategoryId5 +'}';

         */
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var data = 1;
        var urldef = "/blueseal/xhr/MarketplaceAccountInsertManage" + config;
        $.ajax({
            method: "POST",
            url: urldef,
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.showOkBtn();
            });
            bsModal.showOkBtn();
        });
    });
});

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

function addRulesTab() {
    var brandId = $('#brandId').val();
    var shopSupplierId = $('#shopSupplierId').val();


}

function myFunction() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}
function myShopFunction() {
    // Declare variables
    var input, filter, table, tr, td, s, txtValue;
    input = document.getElementById("myShop");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (s = 0; s < tr.length; s++) {
        td = tr[s].getElementsByTagName("td")[1];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[s].style.display = "";
            } else {
                tr[s].style.display = "none";
            }
        }
    }
}

$(document).on('click','#checkedAll',function () {

    $('input:checkbox').not(this).prop('checked', this.checked);

});

