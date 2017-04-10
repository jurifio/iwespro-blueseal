$(document).on('blur', '.translation-element', function (a, b, c) {

    let input = $(this);
    $.ajax({
        type: "PUT",
        url: "#",
        data: {
            string: input.val(),
            hash: input.data('hash'),
            langId: input.data('langId')
        }
    }).done(function (content) {
        new Alert({
            type: "success",
            message: "Traduzione Inserita"
        }).open();

    }).fail(function (content) {
        new Alert({
            type: "warning",
            message: "Errore di inserimento"
        }).open();
    });
});

$(document).on('column-visibility.dt draw.dt', function (e, settings, column, state) {
    if (typeof state == 'undefined' || state) {
        $("textarea:empty").each(function() {
            "use strict";
            let value = $(this).data('encodedValue');
            $(this).html(b64DecodeUnicode(value));
        })
    }
});