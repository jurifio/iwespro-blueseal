$(document).on('bs.brand.edit', function() {
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