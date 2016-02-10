$(document).on('bs.brand.add', function() {

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