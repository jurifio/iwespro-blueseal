$(document).on('bs.brand.edit', function() {

    let descriptionLength = $('#ProductBrand_description')
                                .val()
                                    .length;

    let minLength = 160;
    let maxLength = 2000;

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
            $.each(rawProduct, function (k, v) {
            if(v.responseOk=='1'){
                bodyres+='<div class="row clearfix"><div class="col-md-1"><input id="idTranslation_'+v.idTranslation+'" name="idTranslation_'+v.idTranslation+'" type="hidden" value="'+v.idTranslation+'"/>'+v.idTranslation+'</div>';
                bodyres+='<div class="col-md-1"><input id="langTranslation_'+v.idTranslation+'" name="langTranslation_'+v.idTranslation+'" type="hidden" value="'+v.langId+'"/>'+v.langName+'</div>';
                bodyres+='<div class="col-md-8"><textarea class="form-control" rows="20" cols="180" name="ProductBrandTranslation_'+v.idTranslation+'" id="ProductBrandTranslation_'+v.idTranslation+'">'+v.text+'</textarea></div>';
                bodyres+='<div class="col-md-2"><input id="remoteShopId_'+v.idTranslation+'" name="remoteShopId_'+v.idTranslation+'" type="hidden" value="'+v.remoteShopId+'"/>'+v.remoteShopName+'</div></div>';
            }
            });

            $('#divTranslation').append(bodyres);
        });
        bsModal.showCancelBtn();
        bsModal.addClass('modal-wide');
        bsModal.addClass('modal-high');
        bsModal.setOkEvent(function () {
            /*const data = {

                nameLocation: $('#nameLocation').val(),
                billRegistryClientId: $('#billRegistryClientId').val(),
                addressLocation: $('#addressLocation').val(),
                extraLocation: $('#extraLocation').val(),
                zipCodeLocation: $('#zipCodeLocation').val(),
                cityLocation: $('#cityLocation').val(),
                countryIdLocation: $('#countryIdLocation').val(),
                vatNumberLocation: $('#vatNumberLocation').val(),
                signBoardLocation: $('#signBoardLocation').val(),
                provinceLocation: $('#provinceLocation').val(),
                sdiLocation: $('#sdiLocation').val(),
                contactNameLocation: $('#contactNameLocation').val(),
                phoneLocation: $('#phoneLocation').val(),
                mobileLocation: $('#mobileLocation').val(),
                faxLocation: $('#faxLocation').val(),
                emailLocation: $('#emailLocation').val(),
                emailCcLocation: $('#emailCcLocation').val(),
                emailCcnLocation: $('#emailCcnLocation').val(),
                noteLocation: $('#noteLocation').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/BillRegistryClientLocationManageAjaxController',
                data: data
            }).done(function (res) {

                var bodyLocation = '<tr id="trLocation' + res + '"><td>' + res + '</td><td>' + $('#nameLocation').val() + '</td><td>' + $('#cityLocation').val() + '</td><td><button class="success" id="editLocation" onclick="editLocation(' + res + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                bodyLocation = bodyLocation + '<td><button class="success" id="deleteLocation"  onclick="deleteLocation(' + res + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                $('#myTableLocation').append(bodyLocation);

            }).fail(function (res) {
                bsModalLocation.writeBody('Errore grave');
            }).always(function (res) {
                bsModalLocation.setOkEvent(function () {
                    bsModalLocation.hide();
                    //window.location.reload();
                });
                bsModalLocation.showOkBtn();*/
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
            let bodyres='<div class="row clearfix"><div class="col-md-1"><h4>id</h4></div><div class="col-md-1"><h4>lingua</h4></div><div class="col-md-8"><h4>testo</h4></div><div class="col-md-2"><h4>Shop</h4></div></div>';
            $.each(rawProduct, function (k, v) {
                bodyres+='<div class="row clearfix"><div class="col-md-1"><input id="idTranslation_'+v.idTranslation+'" name="idTranslation_'+v.idTranslation+'" type="hidden" value="'+v.idTranslation+'"/>'+v.idTranslation+'</div>';
                bodyres+='<div class="col-md-1"><input id="langTranslation_'+v.idTranslation+'" name="langTranslation_'+v.idTranslation+'" type="hidden" value="'+v.langId+'"/>'+v.langName+'</div>';
                bodyres+='<div class="col-md-8"><textarea class="form-control" rows="20" cols="180" name="ProductBrandTranslation_'+v.idTranslation+'" id="ProductBrandTranslation_'+v.idTranslation+'">'+v.text+'</textarea></div>';
                bodyres+='<div class="col-md-2"><input id="remoteShopId_'+v.idTranslation+'" name="remoteShopId_'+v.idTranslation+'" type="hidden" value="'+v.remoteShopId+'"/>'+v.remoteShopName+'</div></div>';
            });

            $('#divTranslation').append(bodyres);
        });
        bsModal.showCancelBtn();
        bsModal.addClass('modal-wide');
        bsModal.addClass('modal-high');
        bsModal.setOkEvent(function () {
            /*const data = {

                nameLocation: $('#nameLocation').val(),
                billRegistryClientId: $('#billRegistryClientId').val(),
                addressLocation: $('#addressLocation').val(),
                extraLocation: $('#extraLocation').val(),
                zipCodeLocation: $('#zipCodeLocation').val(),
                cityLocation: $('#cityLocation').val(),
                countryIdLocation: $('#countryIdLocation').val(),
                vatNumberLocation: $('#vatNumberLocation').val(),
                signBoardLocation: $('#signBoardLocation').val(),
                provinceLocation: $('#provinceLocation').val(),
                sdiLocation: $('#sdiLocation').val(),
                contactNameLocation: $('#contactNameLocation').val(),
                phoneLocation: $('#phoneLocation').val(),
                mobileLocation: $('#mobileLocation').val(),
                faxLocation: $('#faxLocation').val(),
                emailLocation: $('#emailLocation').val(),
                emailCcLocation: $('#emailCcLocation').val(),
                emailCcnLocation: $('#emailCcnLocation').val(),
                noteLocation: $('#noteLocation').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/BillRegistryClientLocationManageAjaxController',
                data: data
            }).done(function (res) {

                var bodyLocation = '<tr id="trLocation' + res + '"><td>' + res + '</td><td>' + $('#nameLocation').val() + '</td><td>' + $('#cityLocation').val() + '</td><td><button class="success" id="editLocation" onclick="editLocation(' + res + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                bodyLocation = bodyLocation + '<td><button class="success" id="deleteLocation"  onclick="deleteLocation(' + res + ')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                $('#myTableLocation').append(bodyLocation);

            }).fail(function (res) {
                bsModalLocation.writeBody('Errore grave');
            }).always(function (res) {
                bsModalLocation.setOkEvent(function () {
                    bsModalLocation.hide();
                    //window.location.reload();
                });
                bsModalLocation.showOkBtn();*/
        });
    }

});
