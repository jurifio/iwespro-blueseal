$(document).on('focusout', $('name[descId]').val(), function(event) {
    var changed =  $(event.target);
    $.ajax({
        type: "PUT",
        url: changed.data('action'),
        data: {
            nameId: changed.val(),
            id: changed.data('pid'),
            lang: changed.data('lang')
        }
    });
});

$(document).on('focusin', $('name[descId]').val(), function(event) {
    var textProductDescription = $('textarea[name^="descId"]');

        if (textProductDescription.length) {
            textProductDescription.summernote({
                height: 200
            });
        }


});