var alertHtml = "" +
    "<div class=\"alert alert-dismissible alert-warning fade in\" role=\"alert\">" +
    "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
    "<span aria-hidden=\"true\">&times;</span></button>" +
    "<strong class=\"alert-title\">{title}</strong> <span class=\"alert-message\">{body}</span> </div>";

$(document).on('bs.color.add', function() {

    var alertContainer = $('.alert-container');

    $.ajax({
        type: "POST",
        url:"#",
        data: $('#form-project').serialize()
    }).done(function (){
        new Alert( {
            type: "success",
            message: "Colore inserito correttamente"
        }).open();
        return false;
    }).fail(function (){
        new Alert({
            type: "danger",
            message: "Problema con l'inserimento del colore, riprova"
        }).open();
        return false;
    });

});