$(document).on('bs.brand.edit', function() {
    $.ajax({
        type: "PUT",
        url:"#",
        data: $('#form-project').serialize()
    }).done(function (){
        new Alert({
            type: "success",
            message: "Brand aggiornato correttamente"
        }).open();
        return false;
    }).fail(function (){
        new Alert({
            type: "danger",
            message: "Problema con l'aggiornamento del brand, riprova"
        }).open();
        return false;
    });
});