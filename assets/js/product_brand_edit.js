$(document).on('bs.brand.edit', function() {

    let descriptionLength = $('#ProductBrand_description')
                                .val()
                                    .length;

    let minLength = 1300;
    let maxLength = 2000;

    if(descriptionLength < 1300 || descriptionLength > 2000){
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