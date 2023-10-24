$(document).on('bs.brand.edit', function() {

    let descriptionLength = $('#ProductBrand_description')
                                .val()
                                    .length;

    let minLength = 160;
    let maxLength = 2000;
    var responseOk="1";

    if(descriptionLength < 160 || descriptionLength > 2000){
        new Alert({
            type: "warning",
            message: `La descrizione del brand deve essere compresa fra un numero di caratteri compreso tra ${minLength} e ${maxLength}`
        }).open();
        return false;
    }

    $.ajax({
        type: "PUT",
        url:"#",
        data: $('#form-project').serialize()
    }).done(function (res){
        new Alert({
            type: "success",
            message: "Brand aggiornato correttamente"
        }).open();
        let brand = JSON.parse(res);
        $('#ProductBrand_name').val(brand.name);
        $('#ProductBrand_slug').val(brand.slug);
        $('#ProductBrand_description').val(brand.description);
        $('#ProductBrand_logo').val(brand.logoUrl);
        return false;
    }).fail(function (){
        new Alert({
            type: "danger",
            message: "Problema con l'aggiornamento del brand, riprova"
        }).open();
        return false;
    });
});
$(document).on('bs.brand.translation', function() {
    var responseOk="1";
    if($('#allShops').val()=='1'){
        let bsModal = new $.bsModal('Gestione Traduzioni', {
            body: `<p>Confermare?</p>
 <div id="divTranslation"></div>`
        });
        $.ajax({
            url: '/blueseal/xhr/SelectProductBrandTranslationAjaxController',
            method: 'get',
            data: {
                typeCall: 1,
                productBrandId:$('#ProductBrand_id').val()
            },
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            let rawProduct = res;
            let bodyres='<div class="row clearfix"><div class="col-md-1"><h4>id</h4></div><div class="col-md-1"><h4>lingua</h4></div><div class="col-md-8"><h4>testo</h4></div><div class="col-md-2"><h4>Shop</h4></div></div>';
            var u=0;
            var responseOk='';
            $.each(rawProduct, function (k, v) {
            if(v.responseOk=='1'){
                bodyres+='<div class="row clearfix"><div class="col-md-1"><input id="idTranslation_'+v.idTranslation+'" name="idTranslation_'+v.idTranslation+'" type="hidden" value="'+v.idTranslation+'"/>'+v.idTranslation+'</div>';
                bodyres+='<div class="col-md-1"><input id="langTranslation_'+v.idTranslation+'" name="langTranslation_'+v.idTranslation+'" type="hidden" value="'+v.langId+'"/>'+v.langName+'</div>';
                bodyres+='<div class="col-md-8"><textarea class="form-control" rows="20" cols="180" name="ProductBrandTranslation_'+v.idTranslation+'" id="ProductBrandTranslation_'+v.idTranslation+'">'+v.text+'</textarea></div>';
                bodyres+='<div class="col-md-2"><input id="remoteShopId_'+v.idTranslation+'" name="remoteShopId_'+v.idTranslation+'" type="hidden" value="'+v.remoteShopId+'"/>'+v.remoteShopName+'</div></div>';
            u++;
            responseOk="1";
            }else{
                responseOk="2";
                bodyres='non Ci sono Traduzioni';
            }
            });
            $('#divTranslation').append(bodyres);
        });
        bsModal.showCancelBtn();
        bsModal.addClass('modal-wide');
        bsModal.addClass('modal-high');
        bsModal.setOkEvent(function () {
            if(responseOk==1) {
                var dataId = [];
                var langId = [];
                var textId = [];
                var rshopId = [];
                var a = 0;
                var b = 0;
                var c = 0;
                var d = 0;
                var f = 0;

                $.each($("input[name*='idTranslation_']"), function () {
                    dataId.push($(this).val());
                    a++;
                });
                $.each($("input[name*='langTranslation_']"), function () {
                    langId.push($(this).val());
                    b++;
                });
                $.each($("textarea[name*='ProductBrandTranslation_']"), function () {
                    textId.push($(this).val());
                    c++;
                });
                $.each($("input[name*='remoteShopId_']"), function () {
                    rshopId.push($(this).val());
                    d++;
                });
                var dataVal = [];
                for (f = 0; f < a; f++) {
                    dataVal.push(dataId[f] + '-' + langId[f] + '-' + textId[f] + '-' + rshopId[f] + '-' + $('#ProductBrand_id').val());
                }

                $.ajax({
                    method: 'PUT',
                    url: '/blueseal/xhr/SelectProductBrandTranslationAjaxController',
                    data: {rows: dataVal, typeCall: 1}
                }).done(function (res) {

                    $('#divTranslation').empty().append(res);

                }).fail(function (res) {
                    $('#divTranslation').empty().append(res);
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                        //window.location.reload();
                    });
                    bsModal.showOkBtn();
                });
            }else{
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    //window.location.reload();
                });
            }
        });

    }else{
        let bsModal = new $.bsModal('Gestione Traduzioni', {
            body: `<p>Confermare?</p>
 <div id="divTranslation"></div>`
        });
        $.ajax({
            url: '/blueseal/xhr/SelectProductBrandTranslationAjaxController',
            method: 'get',
            data: {
                typeCall: 2,
                productBrandId:$('#ProductBrand_id').val()
            },
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            let rawProduct = res;
            var u=0;
            let bodyres='<div class="row clearfix"><div class="col-md-1"><h4>id</h4></div><div class="col-md-1"><h4>lingua</h4></div><div class="col-md-8"><h4>testo</h4></div><div class="col-md-2"><h4>Shop</h4></div></div>';
            $.each(rawProduct, function (k, v) {
                bodyres+='<div class="row clearfix"><div class="col-md-1"><input id="idTranslation_'+v.idTranslation+'" name="idTranslation_'+v.idTranslation+'" type="hidden" value="'+v.idTranslation+'"/>'+v.idTranslation+'</div>';
                bodyres+='<div class="col-md-1"><input id="langTranslation_'+v.idTranslation+'" name="langTranslation_'+v.idTranslation+'" type="hidden" value="'+v.langId+'"/>'+v.langName+'</div>';
                bodyres+='<div class="col-md-8"><textarea class="form-control" rows="20" cols="180" name="ProductBrandTranslation_'+v.idTranslation+'" id="ProductBrandTranslation_'+v.idTranslation+'">'+v.text+'</textarea></div>';
                bodyres+='<div class="col-md-2"><input id="remoteShopId_'+v.idTranslation+'" name="remoteShopId_'+v.idTranslation+'" type="hidden" value="'+v.remoteShopId+'"/>'+v.remoteShopName+'</div></div>';
           u++ });

            $('#divTranslation').append(bodyres);
        });
        bsModal.showCancelBtn();
        bsModal.addClass('modal-wide');
        bsModal.addClass('modal-high');
        bsModal.setOkEvent(function () {
            var dataId=[];
            var langId=[];
            var  textId=[];
            var rshopId=[];
            var a=0;
            var b=0;
            var c=0;
            var d=0;
            var f=0;

            $.each($("input[name*='idTranslation_']"),function(){
                dataId.push($(this).val());
                a++;
            });
            $.each($("input[name*='langTranslation_']"),function(){
                langId.push($(this).val());
                b++;
            });
            $.each($("textarea[name*='ProductBrandTranslation_']"),function(){
                textId.push($(this).val());
                c++;
            });
            $.each($("input[name*='remoteShopId_']"),function(){
                rshopId.push($(this).val());
                d++;
            });
            var dataVal=[];
            for (f = 0; f < a; f++) {
                dataVal.push(dataId[f]+'-'+langId[f]+'-'+textId[f]+'-'+rshopId[f]+'-'+$('#ProductBrand_id').val());
            }

            $.ajax({
                method: 'PUT',
                url: '/blueseal/xhr/SelectProductBrandTranslationAjaxController',
                data: {rows: dataVal,typeCall:1}
            }).done(function (res) {

                $('#divTranslation').empty().append(res);

            }).fail(function (res) {
                $('#divTranslation').empty().append(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    }

});
$(document).on('bs.brandtranslation.add', function() {
    var responseOk="1";
    if($('#allShops').val()=='1'){
        let bsModal = new $.bsModal('Gestione Traduzioni', {
            body: `<p>Confermare?</p>
<div id="selectedShop" class="row show"><div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="shopId">Seleziona Lo Shop</label>
                                        <select id="shopId" name="shopId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                        </select>
                                    </div>
                                </div></div>
 <div id="divTranslation"></div>`
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
            var selectShop = $('#shopId');
            if (typeof (selectShop[0].selectize) != 'undefined') selectShop[0].selectize.destroy();
            selectShop.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res2,
            });
        });
        $.ajax({
            url: '/blueseal/xhr/ProductBrandAddTranslationAjaxController',
            method: 'get',
            data: {
                productBrandId:$('#ProductBrand_id').val()
            },
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            let rawProduct = res;
            let bodyres='<div class="row clearfix"><div class="col-md-2"><h4>id</h4></div><div class="col-md-2"><h4>lingua</h4></div><div class="col-md-8"><h4>testo</h4></div></div>';
            var u=0;
             responseOk='';
            $.each(rawProduct, function (k, v) {

                    bodyres+='<div class="row clearfix"><div class="col-md-2"><input id="idTranslation_'+v.idTranslation+'" name="idTranslation_'+v.idTranslation+'" type="hidden" value="'+v.idTranslation+'"/>'+v.idTranslation+'</div>';
                    bodyres+='<div class="col-md-2"><input id="langTranslation_'+v.idTranslation+'" name="langTranslation_'+v.idTranslation+'" type="hidden" value="'+v.langId+'"/>'+v.langName+'</div>';
                    bodyres+='<div class="col-md-8"><textarea class="form-control" rows="20" cols="180" name="ProductBrandTranslation_'+v.idTranslation+'" id="ProductBrandTranslation_'+v.idTranslation+'">'+v.text+'</textarea></div></div>';

            });
            $('#divTranslation').append(bodyres);
        });
        bsModal.showCancelBtn();
        bsModal.addClass('modal-wide');
        bsModal.addClass('modal-high');
        bsModal.setOkEvent(function () {

                var dataId = [];
                var langId = [];
                var textId = [];
                var a = 0;
                var b = 0;
                var c = 0;
                var d = 0;
                var f = 0;

                $.each($("input[name*='idTranslation_']"), function () {
                    dataId.push($(this).val());
                    a++;
                });
                $.each($("input[name*='langTranslation_']"), function () {
                    langId.push($(this).val());
                    b++;
                });
                $.each($("textarea[name*='ProductBrandTranslation_']"), function () {
                    textId.push($(this).val());
                    c++;
                });
                var dataVal = [];
                for (f = 0; f < a; f++) {
                    dataVal.push(dataId[f] + '-' + langId[f] + '-' + textId[f] + '-' + $('#shopId').val() + '-' + $('#ProductBrand_id').val());
                }

                $.ajax({
                    method: 'PUT',
                    url: '/blueseal/xhr/ProductBrandAddTranslationAjaxController',
                    data: {rows: dataVal, shopId: $('#shopId').val()}
                }).done(function (res) {
                    $('#selectedShop').removeClass('show');
                    $('#selectedShop').addClass('hide');
                    $('#divTranslation').empty().append(res);

                }).fail(function (res) {
                    $('#selectedShop').removeClass('show');
                    $('#selectedShop').addClass('hide');
                    $('#divTranslation').empty().append(res);
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                        //window.location.reload();
                    });
                    bsModal.showOkBtn();
                });
        });

    }

});
