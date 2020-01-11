$(document).on('bs.marketplace-account.save', function () {

    $.ajax({
        method: "POST",
        url: "/blueseal/xhr/MarketplaceAccountManage",
        data: {
            lang:$('#lang').val(),
            marketplace_account_name: $('#marketplace_account_name').val(),
            slug:$('#slug').val(),
            nameAdminister:$('#nameAdminister').val(),
            emailNotify:$('#emailNotify').val(),
            isActive: $('#isActive').val(),
            defaultCpcF: $('#defaultCpcF').val(),
            defaultCpcFM: $('#defaultCpcFM').val(),
            defaultCpcM: $('#defaultCpcM').val(),
            defaultCpc: $('#defaultCpc').val(),
            budget01:$('#budget01').val(),
            budget02:$('#budget02').val(),
            budget03:$('#budget03').val(),
            budget04:$('#budget04').val(),
            budget05:$('#budget05').val(),
            budget06:$('#budget06').val(),
            budget07:$('#budget07').val(),
            budget08:$('#budget08').val(),
            budget09:$('#budget09').val(),
            budget10:$('#budget10').val(),
            budget11:$('#budget11').val(),
            budget12:$('#budget12').val(),
            typeInsertion:$('#typeInsertion').val(),
            marketplaceName:$('#marketplaceName'),
            productCategoryIdEx1:$('#productCategoryIdEx1').val(),
            productCategoryIdEx2:$('#productCategoryIdEx2').val(),
            productCategoryIdEx3:$('#productCategoryIdEx3').val(),
            productCategoryIdEx4:$('#productCategoryIdEx4').val(),
            productCategoryIdEx5:$('#productCategoryIdEx5').val(),
            productCategoryIdEx6:$('#productCategoryIdEx6').val(),
            productSizeGroupEx1:$('#productSizeGroupEx1').val(),
            productSizeGroupEx2:$('#productSizeGroupEx2').val(),
            productSizeGroupEx3:$('#productSizeGroupEx3').val(),
            productSizeGroupEx4:$('#productSizeGroupEx4').val(),
            productSizeGroupEx5:$('#productSizeGroupEx5').val(),
            productSizeGroupEx6:$('#productSizeGroupEx6').val(),
            priceModifierRange1:$('#priceModifierRange1').val(),
            priceModifierRange2:$('#priceModifierRange2').val(),
            priceModifierRange3:$('#priceModifierRange3').val(),
            priceModifierRange4:$('#priceModifierRange4').val(),
            priceModifierRange5:$('#priceModifierRange5').val(),
            range1Cpc:$('#range1Cpc').val(),
            range2Cpc:$('#range2Cpc').val(),
            range3Cpc:$('#range3Cpc').val(),
            range4Cpc:$('#range4Cpc').val(),
            range5Cpc:$('#range5Cpc').val(),
            productSizeGroupId1:$('#priceSizeGroupId1').val(),
            productSizeGroupId2:$('#priceSizeGroupId2').val(),
            productSizeGroupId3:$('#priceSizeGroupId3').val(),
            productSizeGroupId4:$('#priceSizeGroupId4').val(),
            productSizeGroupId5:$('#priceSizeGroupId5').val(),
            productCategoryId1:$('#productCategoryId1').val(),
            productCategoryId2:$('#productCategoryId2').val(),
            productCategoryId3:$('#productCategoryId3').val(),
            productCategoryId4:$('#productCategoryId4').val(),
            productCategoryId5:$('#productCategoryId5').val(),


        }
    }).done(function () {
        new Alert({
            type: "success",
            message: "Inserimento Eseguito"
        }).open();
    }).fail(function (e) {
        console.log(e);
        new Alert({
            type: "danger",
            message: "Impossibile Salvare"
        }).open();
    });
});

$(document).ready(function () {

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
$('#selectCreation').change(function () {
    if ($('#selectCreation').val() == 1){
        $('#divmarketplace').empty();
        $('#divmarketplace').append(`
         <div class="col-md-12">
                                        <div class="form-group form-group-default required">
                                            <input type="hidden" id="typeInsertion" name="typeInsertion" value="1"/>
                                            <label for="marketplaceName">Nome Aggregatore</label>
                                            <input id="marketplaceName" autocomplete="off" type="text"
                                                   class="form-control" name="marketplaceName" value=""
                                                   required="required"/>
                                            <span class="bs red corner label"><i
                                                        class="fa fa-asterisk"></i></span>
                                        </div>
                                    </div>
        
        `);
    }else{
        $('#divmarketplace').empty();
        $('#divmarketplace').append(`
         <div class="col-md-12">
                                        <div class="form-group form-group-default required">
                                         <input type="hidden" id="typeInsertion" name="typeInsertion" value="2"/>
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="marketplaceName">Nome Aggregatore
                                                </label>
                                                <select id="marketplaceName"
                                                        name="marketplaceName"
                                                        class="full-width selectpicker"
                                                        placeholder="Selezione l'aggregatore "
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
                table: 'Marketplace',
                condition :{type:'cpc'}

            },
            dataType: 'json'
        }).done(function (res2) {
            var select = $('#marketplaceName');
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