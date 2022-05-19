$(document).on('bs.brand.add', function() {
if($('#shopId').val()==''){
    new Alert({
        type: "danger",
        message: "Devi Selezionare lo shop"
    }).open();
    return false;
}
    if($('#ProductBrand_name').val()==''){
        new Alert({
            type: "danger",
            message: "Devi Inserire il nome del Brand"
        }).open();
        return false;
    }
    $.ajax({
        type: "POST",
        url:"#",
        data: $('#form-project').serialize()
    }).done(function (){
        new Alert({
            type: "success",
            message: "Brand inserito correttamente"
        }).open();
        return false;
    }).fail(function (){
        new Alert({
            type: "danger",
            message: "Problema con l'inserimento del Brand, riprova"
        }).open();
        return false;
    });

});
if($('#allShops').val()==1) {
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
}else{
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop',
            condition: {id: $('#shopSelected').val()}
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
}