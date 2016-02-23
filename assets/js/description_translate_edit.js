$(document).ready(function() {

    var textProductDescription = $('textarea[name^="ProductDescription"]');
    textProductDescription.each(function () {
        if (textProductDescription.length) {
            textProductDescription.summernote({
                height: 200
            });
        }
    });

});

$(document).on('bs.desc.edit', function (e,element,button) {

    $.ajax({
        type: "PUT",
        url:"#",
        data: $('#form-project').serialize()
    }).done(function (){
        new Alert({
            type: "success",
            message: "Descrizioni tradotte correttamente"
        }).open();
        return false;
    }).fail(function (){
        new Alert({
            type: "danger",
            message: "Problema con la traduzione delle descrizioni, riprova"
        }).open();
        return false;
    });
});